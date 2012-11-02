<?php
/**
 * Show the sky scanner widget
 *
 * @package Br
 * @author Bartosz Rychlicki <b@br-design.pl>
 */
class Trendmed_View_Helper_ShowSkyscanner extends Zend_View_Helper_Abstract
{
    public function ShowSkyscanner($language = null)
    {
        $cultureCodeMapping = array(
            'pl_PL' => 'pl',
            'de_de' => 'de',
        );
        $config = Zend_Registry::get('config');
        $apiKey = $config->widgets->skyscanner->apiKey;
        if(isset($cultureCodeMapping[$language])) {
            $culture = ",{cultureid:'$cultureCodeMapping[$language]'}";
        }

        $output = <<<EOT
<script type="text/javascript" src="http://api.skyscanner.net/api.ashx?key=$apiKey"></script>
<script type="text/javascript">
    skyscanner.load('snippets','2'$culture);
    function main(){
        var snippet=new skyscanner.snippets.SearchPanelControl();
        snippet.setCurrency('PLN');
        snippet.setShape('box300x250');
        snippet.setDeparture('pl');
        snippet.draw(document.getElementById('snippet_searchpanel'));
        }
    skyscanner.setOnLoadCallback(main);
</script>

<div id="snippet_searchpanel" style="width: auto; height:auto;"></div>
EOT;
        return $output;
    }
}
