<?php
require_once 'abstract.php';

class Mage_Shell_301_Generator extends Mage_Shell_Abstract
{
    public function run()
    {
        $path = Mage::getBaseDir() . '/301_csv';

        if(is_dir($path))
        {
            $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
            $newFile = Mage::getBaseDir() . DS . '301redirections';
            if(file_exists($newFile)) unlink($newFile);

            foreach($objects as $name => $object) {
                if($object->getFileName() != '.' && $object->getFileName() != '..')
                {
                    $file = new SplFileObject($name);
                    $delimiter = ';';
                    $enclosure = "\"";
                    $file->setCsvControl($delimiter, $enclosure);
                    while ($file->valid()) {
                        $data = $file->fgetcsv();
                        if(is_array($data) && !empty($data[0]) && !empty($data[1]) && strpos($data[0], 'http') !== false && strpos($data[1], 'http') !== false)
                        {
                            $current = file_get_contents($newFile);

                            $oldLink = $data[0];
                            $oldLink = str_replace('http://www.thelaundress.com/','^', $oldLink);
                            $oldLink = str_replace('http://store.thelaundress.com/','^', $oldLink);
                            $oldLink = str_replace('.','\.', $oldLink);

                            if(substr($oldLink, 1, 1) == ' ' || substr($oldLink, 1, 1) == '(') continue;

                            $newLink = $data[1];
                            $newLink = trim($newLink);

                            $rewriteQuery = array();

                            if(substr($newLink, 0, 1) == '/' && strpos($newLink, '#') === false)
                            {
                                //$newLink = substr($newLink, 1);
                                //$newLink  =  '^' . $newLink;
                            }

                            if(!empty($newLink))
                            {
                                $newLinkArray = explode(' ', $newLink);
                                if(count($newLinkArray) > 1)
                                {
                                    foreach($newLinkArray as $newLinkItem)
                                    {
                                        $newLinkItem = trim($newLinkItem);
                                        if(!empty($newLinkItem))
                                        {
                                            $newLinkItem = str_replace('http://thelaund.nextmp.net/', '/', $newLinkItem);
                                            $newLinkItem = str_replace('.', '\.', $newLinkItem);
                                            //echo 'RewriteRule ' . $oldLink  . ' ' . $newLinkItem . ' [R=301,L]' . "\n";

                                            if(strpos($newLink, '#') !== false)
                                            {
                                                $flag = ' [NE,R=301,L]';
                                            } else{
                                                $flag = ' [R=301,L]';
                                            }

                                            if(strpos($oldLink, '?') !== false)
                                            {
                                                $oldLinkQuery = explode('?',$oldLink);
                                                if(!in_array($oldLinkQuery[1], $rewriteQuery))
                                                {
                                                    $rewriteRule     = "\n" . 'RewriteCond %{QUERY_STRING} ^' . $oldLinkQuery[1] . '$' . "\n";
                                                    $rewriteRule    .= 'RewriteRule ' . $oldLinkQuery[0]  . ' ' . $newLinkItem . '?' . $flag . "\n" . "\n";
                                                    $rewriteQuery[]  = $oldLinkQuery[1];
                                                }


                                            } else {
                                                $rewriteRule = 'RewriteRule ' . $oldLink  . ' ' . $newLinkItem . $flag . "\n";
                                            }



                                            $current .= $rewriteRule;
                                        }
                                    }
                                }
                                else{
                                    $newLink = str_replace('http://thelaund.nextmp.net/', '/', $newLink);
                                    $newLink = str_replace('.', '\.', $newLink);
                                    //echo 'RewriteRule ' . $oldLink  . ' ' . $newLink . ' [R=301,L]' . "\n";
                                    if(strpos($newLink, '/#') !== false)
                                    {
                                        $flag = ' [NE,R=301,L]';
                                    } else{
                                        $flag = ' [R=301,L]';
                                    }

                                    if(strpos($oldLink, '?') !== false)
                                    {
                                        $oldLinkQuery = explode('?',$oldLink);
                                        $rewriteRule = "\n" . 'RewriteCond %{QUERY_STRING} ^' . $oldLinkQuery[1] . '$' . "\n";;
                                        $rewriteRule .= 'RewriteRule ' . $oldLinkQuery[0]  . ' ' . $newLink . '?' . $flag . "\n" . "\n";

                                    } else {
                                        $rewriteRule = 'RewriteRule ' . $oldLink  . ' ' . $newLink . $flag . "\n";
                                    }

                                    $current .= $rewriteRule;
                                    //$current .= 'RewriteRule ' . $oldLink  . ' ' . $newLink . ' [R=301,L]' . "\n";
                                }

                                file_put_contents($newFile, $current);
                            }
                        }
                    }
                }

            }
        }


    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f 301_csv.php
USAGE;
    }
}

$shell = new Mage_Shell_301_Generator();
$shell->run();
