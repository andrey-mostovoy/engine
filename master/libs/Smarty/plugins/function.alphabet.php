<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.alphabet.php
 * Type:     function
 * Name:     a2z
 * Purpose:  Prints out letters (A-Z) with a link
 * -------------------------------------------------------------
 * @author Anna Petrashova
 * @param array $params parameters
 *            - begin       (char) - first symbol
 *            - end         (char) - last symbol
 *            - url         (string) - url
 *            - name        (string) - $_GET param for link
 *            - active      (char) - active letter
 *            - friendly_urls   (bool) - the output next to each radio button
 *            - li_style     (string) - styles for <li>
 *            - li_class     (string) - classes for <li>
 *            - case_sensitive     (optional) - assign the output as an array to this variable
 * @return list of symbols with links 
 */

function smarty_function_alphabet($params=array(), &$smarty)
{
   $params['begin'] = !empty($params['begin'])?$params['begin']:'A';
   $params['end'] = !empty($params['end'])?$params['end']:'Z';
   $params['url'] = !empty($params['url'])?$params['url']:'letter.php';
   $params['name'] = !empty($params['name'])?$params['name']:'L';
   $params['active'] = !empty($params['active'])?$params['active']:false;
   $params['friendly_urls'] = !empty($params['friendly_urls'])?$params['friendly_urls']:true;
   $params['li_style'] = !empty($params['li_style'])?' style="'.$params['li_style'].'" ':'';
   $params['li_class'] = !empty($params['li_class'])?' class="'.$params['li_class'].'" ':'';
   $params['case_sensitive'] = !empty($params['case_sensitive'])?$params['case_sensitive']:false;
   
   $links = array();
   for($letter = ord($params['begin']); $letter <= ord($params['end']); ++$letter)
   {
      $alphabet = chr($letter);
      $active = ($alphabet==$params['active'] || (!$params['case_sensitive'] && ($alphabet==ucfirst($params['active']) || $alphabet==lcfirst($params['active']))))?' class="active" ':'';
      if ($params['friendly_urls']) {
          $links[] = "<li{$active}{$params['li_style']}{$params['li_class']}><span><a href='{$params['url']}/{$params['name']}/{$alphabet}'>{$alphabet}</a></span></li>";
      } else {
          $links[] = "<li{$active} {$params['li_style']}{$params['li_class']}><span><a href='{$params['url']}?{$params['name']}={$alphabet}'>{$alphabet}</a></span></li>";
      }
   }
   return(implode('', $links));
}
?>