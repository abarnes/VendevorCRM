<?php
class QualifyShell extends AppShell {
    public $uses = array('Lead');
    
    public function main() {
        App::import('Vendor', 'simple_html_dom');
        App::uses('HttpSocket', 'Network/Http');
        date_default_timezone_set('America/New_York');

        $this->Lead->recursive = 0;
        //$conditions = array('Lead.website !='=>'','Lead.id'=>array(27,98,5088,6723,7390,8286,8574,9046,9865,11257,12615,13071,13571,14126,14942,15547,16056,17118,23245,24260));
        $conditions = array('Lead.website !='=>'','Lead.status'=>0,'Lead.id >'=>10074/*,'Lead.id'=>array(20994,21018,21045,21057,21058,21059,21063,21133,21201,21260,21282,21293,21315,21322,21329,21343,21376,21378,21402,21446,21451,21520,21521,21525,21526,21528,21529,21533,21547)*/);
        $leads = $this->Lead->find('all',array('conditions'=>$conditions,'fields'=>array('Lead.id','Lead.name','Lead.website')));

        foreach($leads as $l) {
            $this->out($l['Lead']['id'].' '.$l['Lead']['name']);
            $check = $this->_check_page($l['Lead']['website'],$l['Lead']['id'],true,$l['Lead']['website']);
            //$this->out('check: '.$check);
            if ($check===true) {
                $this->out($l['Lead']['id'].' Potentially Qualified');
            } else {
                $this->out($l['Lead']['id'].' Unqualified');
                if ($check>0 && $check!=2) $this->_unqualified($l['Lead']['id'],$check);
            }
            $this->hr();
        }

        $this->out(date('M d Y H:i:s',strtotime("now")).' finished qualifying.');
        $this->hr();
    }

    function _check_page($url,$id,$deep,$main_url) {
        $result = $this->_curl($url);
        if (!$result) {
            $this->out('Error 1 (site unreachable) '.$id);
            return 1;
        } else {
            $html = new simple_html_dom();
            if ($html->load($result)) {
                $check = $this->_competitors($html);
                if (!$check) {
                    $check = $this->_keywords($html,$id,$deep,$main_url);
                    if (!$check) {
                        return true;
                    } else {
                        return $check;
                    }
                } else {
                    return $check;
                }
            } else {
                $this->out('Error 2 (bad html) '.$id);
            }
        }

        return false;
    }

    function _curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.45 Safari/535.19');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);

        if ($output=="") {
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE)!=200) {
                $this->out($url.' returned HTTP '.curl_getinfo($ch, CURLINFO_HTTP_CODE));
                return false;
            }
        }
        curl_close($ch);

        return $output;
    }

    function _competitors($html) {
        //most competitors - script src
        $script_src_check = array(3=>'bigcommerce.com',4=>'shopify.com',5=>'volusion.com',6=>'gostorego.com',7=>'bigcartel.com',8=>'wazala.com');
        foreach ($html->find('script[src]') as $s) {
            foreach ($script_src_check as $k=>$c) {
                if (strpos($s->src,$c)) {
                    $this->out('Error '.$k.' ('.$c.')');
                    return $k;
                }
            }
        }

        //3d cart - comment
        $comment_check = array(9=>'3dcart',10=>'UniteU');
        foreach ($html->find('comment') as $g) {
            foreach ($comment_check as $k=>$c) {
                if (strpos($g,$c)) {
                    $this->out('Error '.$k.' ('.$c.')');
                    return $k;
                }
            }
        }

        /*if (strpos($body,"PayPalButton")) {
            $this->out('Error 10 (PayPal button)');
            return true;
        }*/
        //paypal - form
        foreach($html->find('form') as $f) {
            if (strpos($f->action,"https://www.paypal.com")) {
                $this->out('Error 11 (PayPal button)');
                return 11;
            }
        }

        return false;
    }

    function _keywords($html,$id,$deep,$main_url) {
        foreach ($html->find('div') as $d) {
            if (strpos($d->plaintext,"This Web page is parked for FREE, courtesy of GoDaddy.com")!==false) {
                $this->out('Error 32 (GoDaddy parked domain)');
                return 32;
            }
        }

        foreach ($html->find('a') as $a) {
            //search for "cart"
            if (strpos(strtolower($a->outertext),"cart")) {
                $this->out('Error 31 ("Cart" found)');
                return 31;
            } else {
                foreach ($a->find('img') as $i) {
                    if (strpos(strtolower($i->src),"cart") || strpos(strtolower($i->alt),"cart")) {
                        $this->out('Error 31 ("Cart" found)');
                        return 31;
                    }
                }
            }
        }

        foreach ($html->find('input[type=image]') as $a) {
            //search for "cart"
            if (strpos(strtolower($a->outertext),"cart")) {
                $this->out('Error 31 ("Cart" found)');
                return 31;
            }
        }

        //search for store, shop, or product link (disabled if already on secondary page)
        if ($deep) {
            $checked_urls=array();
            foreach ($html->find('a') as $a) {
                if (
                        (
                            strpos(strtolower($a->href),"shop")!==false ||
                            strpos(strtolower($a->plaintext),"shop")!==false ||
                            strpos(strtolower($a->href),"store")!==false ||
                            strpos(strtolower($a->plaintext),"store")!==false
                        ) &&
                        !strpos(strtolower($a->outertext),"locat") &&
                        !strpos(strtolower($a->outertext),"hour")
                    )
                {
                    if (!in_array($a->href,$checked_urls)) {
                        $checked_urls[] = $a->href;
                    }
                } else {
                    //check for "products", "rentals", "classes"
                    //return false;

                }
            }

            if (!empty($checked_urls)) {
                foreach ($checked_urls as $k=>$u) {
                    //$this->out($u);
                    if ($u==$main_url.'/' || $u==$main_url) {
                        unset($checked_urls[$k]);
                    } else {
                        if (strpos($u,'http')===false) {
                            //$this->out('fixing '.$u);
                            $m_url = parse_url($main_url);
                            if (substr($u,0,1)!='/') $u = '/'.$u;
                            $checked_urls[$k] = $m_url['scheme'].'://'.$m_url['host'].$u;
                        }
                    }
                }
            }
            $checked_urls = array_slice($checked_urls,0,50);

            if (!empty($checked_urls)) {
                foreach ($checked_urls as $u) {
                    $this->out('Checking page '.$u);
                    //make sure it doesn't link to a known shopping cart/registration system
                    $known = array(12=>'mindbodyonline.com');
                    foreach ($known as $key=>$k) {
                        if (strpos($u,$k)!==false) {
                            $this->out('Error 12 (mindbodyonline.com)');
                            return $key;
                        }
                    }

                    $check = $this->_check_page($u,$id,false,$main_url);
                    if ($check!==true) {
                        if ($check==1) $check = 0;
                        return $check;
                    }
                }
            }

        }

        return false;
    }

    public function _unqualified($id,$reason) {
        $this->Lead->id = $id;
        $this->Lead->save(array('Lead'=>array('status'=>3,'unqualified_reason'=>$reason)));
        $this->Lead->id = false;
        return true;
    }
}
?>