<?php
namespace Service;

/**
 * 分页
 * 
 * @author    zengxiong
 * @since     2017年8月3日
 * @version   1.0
 */
class pager
{
    /**
     * 获取分页
     * 
     * @param unknown $total 总量
     * @param unknown $page  当前页码
     * @param unknown $size  每页大小
     * @author zengxiong
     * @since  2017年8月3日
     */
    public static function getPager($total,$page,$size = 10) {
        
        $realPageCount = $total / $size;
        $pageCount = is_int($realPageCount)?$realPageCount:floor($realPageCount)+1;
        $curPage = $page;
        $showPagesCount = 10;
        $mid = ceil($showPagesCount / 2) - 1;
        
        if ($pageCount <= $showPagesCount) {
            $from = 1;
            $to = $pageCount;
        } else {
            $from = $curPage <= $mid ? 1 : $curPage - $mid + 1;
            $to = $from + $showPagesCount - 1;
            $to > $pageCount && $to = $pageCount;
        }
        
        $pages = [];
        $pageFirst = '';
        $pageFirstDot = '';
        $pageLast = '';
        $pageLastDot = '';
        
        for ($i = $from; $i <= $to; $i++) {
            $pages[$i] = $i;
        }
        
        if (($curPage - $from) < ($curPage - 1) && $pageCount > $showPagesCount) {
            $pageFirst = 1;
            if (($curPage - 1) - ($curPage - $from) != 1) {
                $pageFirstDot = '...';
            }
        }
        if (($to - $curPage) < ($pageCount - $curPage) && $pageCount > $showPagesCount) {
            $pageLast = $pageCount;
            if (($pageCount - $curPage) - ($to - $curPage) != 1) {
                $pageLastDot = '...';
            }
        }
        $pagePrev = $curPage > $from ? $curPage - 1 : '';
        $pageNext = $curPage < $to ? $curPage + 1 : '';
        
        $str ="<div class='col-md-12 text-center'><ul class='pagination pagination-md' id='pager'>";
        
        if(!empty($pageFirst)){
            $str .= "<li><a href='###' data-page='".$pageFirst."'>".$pageFirst."</a></li>";
        }
        
        if(!empty($pageFirstDot)){
            $str .= "<li><a href='###'>".$pageFirstDot."</a></li>";
        }
        
        if(!empty($pages)){
            foreach ($pages as $page){
                $str .= "<li><a href='###' data-page='".$page."'>".$page."</a></li>";
            }
        }
            
        if(!empty($pageLastDot)){
            $str .= "<li><a href='###'>".$pageLastDot."</a></li>";
        }
        
        if(!empty($pageLast)){
            $str .= "<li><a href='###' data-page='".$pageLast."'>".$pageLast."</a></li>";
        }
        
        $str .= "<li><span>共 ".$total." 条记录</span></li></ul></div>";
        
        return $str;
    }
}

