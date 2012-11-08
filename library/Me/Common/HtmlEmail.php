<?php
namespace Me\Common;

/**
 * Created by JetBrains PhpStorm.
 * User: bard
 * Date: 30.10.12
 * Time: 23:09
 * To change this template use File | Settings | File Templates.
 */
class HtmlEmail
{
    static public function embedFooterLogo(\Zend_Mail $mail)
    {
        $logoImage = __DIR__.'/../../../public/img/logo.png';

        // image paths relative to this script

        $message = '<img src="cid:logo" title="Trendmed.eu" alt="Trendmed.eu logo" />';

        $body = $mail->getBodyHtml(true);

            if (is_file($logoImage))
            {
                $idata = file_get_contents($logoImage);

                if ($itype = self::get_image_type($logoImage))
                {
                    // Attach the Image
                    $img = $mail->createAttachment($idata, $itype, \Zend_Mime::DISPOSITION_INLINE, \Zend_Mime::ENCODING_BASE64);

                    $img->id = "cid:logo";
                    $body .= $message;
                }
            }
            else
            {
                error_log('The image "'.$logoImage.'" does not exist in function '.__FUNCTION__.' on line '.__LINE__.' of file '.__FILE__);
            }
        $mail->setType(Zend_Mime::MULTIPART_RELATED);
        $mail->setBodyHtml($body);
        return $mail;
    }

    /**
     * Return the MIME type for an image file
     *
     * @param string $image_path Path to image file
     * @return bool|string
     */
    static public function get_image_type($image_path)
    {
        if ( ! file_exists($image_path))
        {
            error_log('The file "'.$image_path.'" does not exist in function '.__FUNCTION__.' on line '.__LINE__.' of file '.__FILE__);

            return false;
        }

        $itype = false;

        if (function_exists('exif_imagetype') && function_exists('image_type_to_mime_type'))
        {
            $itype = image_type_to_mime_type(exif_imagetype($image_path));
        }
        else
        {
            if ($info = getimagesize($image_path))
            {
                $type = $info[2];
            }
            else
            {
                $type = pathinfo($image_path, PATHINFO_EXTENSION);
            }

            switch(strtolower($type))
            {
                case 'gif':     $itype = 'image/gif';
                    break;
                case 'png':     $itype = 'image/png';
                    break;
                case 'jpg':     $itype = 'image/jpeg';
                    break;
                case 'jpeg':    $itype = 'image/jpeg';
                    break;
                case 'swf':     $itype = 'application/x-shockwave-flash';
                    break;
                case 'psd':     $itype = 'image/psd';
                    break;
                case 'bmp':     $itype = 'image/bmp';
                    break;
                case 'tiff':    $itype = 'image/tiff';
                    break;
                case 'jpc':     $itype = 'application/octet-stream';
                    break;
                case 'jp2':     $itype = 'image/jp2';
                    break;
                case 'jpx':     $itype = 'application/octet-stream';
                    break;
                case 'jb2':     $itype = 'application/octet-stream';
                    break;
                case 'swc':     $itype = 'application/x-shockwave-flash';
                    break;
                case 'iff':     $itype = 'image/iff';
                    break;
                case 'wbmp':    $itype = 'image/vnd.wap.wbmp';
                    break;
                case 'xbm':     $itype = 'image/xbm';
                    break;
                case 'ico':     $itype = 'image/vnd.microsoft.icon';
                    break;

                default:        $itype = 'application/octet-stream';
            }
        }

        return $itype;
    }
}