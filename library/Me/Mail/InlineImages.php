<?php
class Me_Mail_InlineImages extends Zend_Mail
{
    public function buildHtml()
    {

        $paths = explode(PATH_SEPARATOR, get_include_path());
        $paths[] = __FILE__.'/../../../public';
        set_include_path(implode(PATH_SEPARATOR, $paths));


        // Important, without this line the example don't work!
        // The images will be attached to the email but these will be not
        // showed inline
        $this->setType(Zend_Mime::MULTIPART_RELATED);
        $matches = array();
        preg_match_all("#<img.*?src=*['\"]file://([^'\"]+)#i",
            $this->getBodyHtml(true),
            $matches);
        $matches = array_unique($matches[1]);
        if (count($matches ) > 0) {
            foreach ($matches as $key => $filename) {
                if (is_readable($filename)) {

                    $at = $this->createAttachment(file_get_contents($filename));
                    $at->type = $this->mimeByExtension($filename);
                    $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                    $at->encoding = Zend_Mime::ENCODING_BASE64;
                    $at->id = 'cid_' . md5_file($filename);
                    $this->setBodyHtml(str_replace('file://' . $filename,
                            'cid:' . $at->id,
                            $this->getBodyHtml(true)),
                        'UTF-8',
                        Zend_Mime::ENCODING_8BIT);
                }
            }
        }
    }

    public function mimeByExtension($filename)
    {
        if (is_readable($filename) ) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            switch ($extension) {
                case 'gif':
                    $type = 'image/gif';
                    break;
                case 'jpg':
                case 'jpeg':
                    $type = 'image/jpg';
                    break;
                case 'png':
                    $type = 'image/png';
                    break;
                default:
                    $type = 'application/octet-stream';
            }
        }

        return $type;
    }

    public function send($transport = null)
    {
        $this->buildHtml();
        return parent::send($transport);
    }

    public function setBodyHtml($html, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        return parent::setBodyHtml($html, 'UTF-8', \Zend_Mime::MULTIPART_RELATED);
    }


}