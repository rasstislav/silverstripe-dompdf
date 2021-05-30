<?php

namespace TJBW\SS_DOMPDF\Dev;

use Dompdf\Dompdf;
use FontLib\Font;
use SilverStripe\Control\Director;
use SilverStripe\Dev\BuildTask;

class LoadFontTask extends BuildTask
{
    protected $title = "Load font";

    public function run($request) {
        if (!Director::is_cli()) {
            echo "only for command line";
            return;
        }

        $getVars = $request->getVars();
        unset($getVars['url']);

        if ($_SERVER["argc"] < 4) {
            echo $this->usage();
        }
        else {
            $dompdf = new Dompdf();

            call_user_func_array(array($this, "install_font_family"), array_merge(array($dompdf), array_slice($_SERVER["argv"], 2)));
        }
    }

    private function usage() {
        return <<<EOD
Usage: php {$_SERVER["argv"][0]} {$_SERVER["argv"][1]} [n_file [b_file] [i_file] [bi_file]]

font_family:      the name of the font, e.g. Verdana, 'Times New Roman',
                  monospace, sans-serif. If it equals to "system_fonts",
                  all the system fonts will be installed.

n_file:           the .ttf or .otf file for the normal, non-bold, non-italic
                  face of the font.

{b|i|bi}_file:    the files for each of the respective (bold, italic,
                  bold-italic) faces.

If the optional b|i|bi files are not specified, load_font.php will search
the directory containing normal font file (n_file) for additional files that
it thinks might be the correct ones (e.g. that end in _Bold or b or B).  If
it finds the files they will also be processed.  All files will be
automatically copied to the DOMPDF font directory, and afm files will be
generated using php-font-lib (https://github.com/PhenX/php-font-lib).

Examples:

php {$_SERVER["argv"][0]} {$_SERVER["argv"][1]} silkscreen /usr/share/fonts/truetype/slkscr.ttf
php {$_SERVER["argv"][0]} {$_SERVER["argv"][1]} 'Times New Roman' /mnt/c_drive/WINDOWS/Fonts/times.ttf
EOD;
    }

    public function install_font_family($dompdf, $fontname, $normal, $bold = null, $italic = null, $bold_italic = null) {
        $fontMetrics = $dompdf->getFontMetrics();

        // Check if the base filename is readable
        if ( !is_readable($normal) )
            throw new \Exception("Unable to read '$normal'.");

        $dir = dirname($normal);
        $basename = basename($normal);
        $last_dot = strrpos($basename, '.');
        if ($last_dot !== false) {
            $file = substr($basename, 0, $last_dot);
            $ext = strtolower(substr($basename, $last_dot));
        } else {
            $file = $basename;
            $ext = '';
        }

        if ( !in_array($ext, array(".ttf", ".otf")) ) {
            throw new Exception("Unable to process fonts of type '$ext'.");
        }

        // Try $file_Bold.$ext etc.
        $path = "$dir/$file";

        $patterns = array(
            "bold"        => array("_Bold", "b", "B", "bd", "BD"),
            "italic"      => array("_Italic", "i", "I"),
            "bold_italic" => array("_Bold_Italic", "bi", "BI", "ib", "IB"),
        );

        foreach ($patterns as $type => $_patterns) {
            if ( !isset($$type) || !is_readable($$type) ) {
                foreach($_patterns as $_pattern) {
                    if ( is_readable("$path$_pattern$ext") ) {
                        $$type = "$path$_pattern$ext";
                        break;
                    }
                }

                if ( is_null($$type) )
                    echo ("Unable to find $type face file.\n");
            }
        }

        $fonts = compact("normal", "bold", "italic", "bold_italic");
        $entry = array();

        // Copy the files to the font directory.
        foreach ($fonts as $var => $src) {
            if ( is_null($src) ) {
                $entry[$var] = $dompdf->getOptions()->get('fontDir') . '/' . mb_substr(basename($normal), 0, -4);
                continue;
            }

            // Verify that the fonts exist and are readable
            if ( !is_readable($src) )
                throw new Exception("Requested font '$src' is not readable");

            $dest = $dompdf->getOptions()->get('fontDir') . '/' . basename($src);

            if ( !is_writeable(dirname($dest)) )
                throw new Exception("Unable to write to destination '$dest'.");

            echo "Copying $src to $dest...\n";

            if ( !copy($src, $dest) )
                throw new Exception("Unable to copy '$src' to '$dest'");

            $entry_name = mb_substr($dest, 0, -4);

            echo "Generating Adobe Font Metrics for $entry_name...\n";

            $font_obj = Font::load($dest);
            $font_obj->saveAdobeFontMetrics("$entry_name.ufm");
            $font_obj->close();

            $entry[$var] = $entry_name;
        }

        // Store the fonts in the lookup table
        $fontMetrics->setFontFamily($fontname, $entry);

        // Save the changes
        $fontMetrics->saveFontFamilies();
    }
}
