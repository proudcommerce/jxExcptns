<?php

/*
 *    This file is part of the module jxExcptns for OXID eShop Community Edition.
 *
 *    The module OxProbs for OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    The module OxProbs for OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      https://github.com/job963/jxExcptns
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @copyright (C) Joachim Barthel 2012-2013
 *
 */
 
class jxexcptns extends oxAdminView
{
    protected $_sThisTemplate = "jxexcptns.tpl";
    public function render()
    {
        parent::render();
        $oSmarty = oxUtilsView::getInstance()->getSmarty();
        $oSmarty->assign( "oViewConf", $this->_aViewData["oViewConf"]);
        $oSmarty->assign( "shop", $this->_aViewData["shop"]);
        $myConfig = $this->getConfig();
        $sLogsDir = $myConfig->getLogsDir();

        $sLogFile = $sLogsDir . 'EXCEPTION_LOG.txt';
        $fp = fopen($sLogFile, 'r');
        $aTemp = array();
        if ($fp == FALSE)
            $aContent[0] = 'File EXCEPTION_LOG.txt not found.';

        while (!feof($fp)) 
            array_push( $aTemp, fgets($fp) );
        fclose($fp);

        $aContent = array();
        if (count($aTemp) < 400)
            $iStart = 0;
        else
            $iStart = count($aTemp) - 400;
        
        $iStop = count($aTemp);
        for ( $i=$iStart; $i<$iStop; $i++ )
            array_push ($aContent, $this->keywordHighlighter($aTemp[$i]));
        
        $oSmarty->assign("aContent",$aContent);

        return $this->_sThisTemplate;
    }
    
    
    public function keywordHighlighter( $sText ) 
    {
        $sStylePreHeader = '<span style="font-size:1.25em; font-weight:bold;">';
        $sStylePostHeader = '</span>';
        $sStylePreError = '<span style="font-weight:bold;color:#dd0000;">';
        $sStylePostError = '</span>';
        
        $aSearch = array(
            '/(.*)(Faulty component|Connection Error)(.*)/',
            '/(.*)\\[0]:(.*)/',
            );

        $aReplace = array(
            "{$sStylePreHeader}$0{$sStylePostHeader}",
            "$1[0]: {$sStylePreError}$2{$sStylePostError}",
            );
        
        $sText = preg_replace($aSearch, $aReplace, $sText);

        return $sText;
    }
    
    
    public function fileDownload()
    {
        $myConfig = $this->getConfig();
        $sLogsDir = $myConfig->getLogsDir();
        $sLogFile = 'EXCEPTION_LOG.txt';

        header("Content-Type: text/plain");
        header("Content-Disposition: attachment; filename=\"$sLogFile\"");
        readfile($sLogsDir.$sLogFile);
    }
    
}
?>