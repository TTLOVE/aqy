<?php
$realPageCount = $pager['total'] / $pager['size'];
$pageCount = is_int($realPageCount)?$realPageCount:floor($realPageCount)+1;
$curPage = $pager['page'];
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

$uri = $_SERVER['REQUEST_URI'];
if ( !stripos($uri, "?") ) {
    $uri .= '?page=';
} else {
    $uriArr = explode('?', $uri);
    $params = $_SERVER["QUERY_STRING"];
    parse_str($params, $params);
    unset($params['page']);
    if ( empty($params) ) {
        $uri = $uriArr[0] . '?page=' ;
    } else {
        $uri = $uriArr[0] . '?' . http_build_query($params);
        $uri .= '&page=';
    }
}

?>

<div class="col-md-12 text-center">
    <ul class="pagination pagination-md" id="pager">
        @if (!empty($pageFirst))
            <li><a href="{{$uri}}{{$pageFirst}}" data-page="{{$pageFirst}}">{{$pageFirst}}</a>
            </li>
        @endif
        @if (!empty($pageFirstDot))
            <li><a href="###">{{$pageFirstDot}}</a></li>
        @endif
        @if (!empty($pages))
            @foreach ($pages as $page)
                @if ($page == $curPage)
                    <li><a href="{{$uri}}{{$page}}" style="background-color: rgb(66, 139, 202); border-color: rgb(66, 139, 202); color: rgb(255, 255, 255);" data-page="{{$page}}">{{$page}}</a></li>
                @else
                    <li><a href="{{$uri}}{{$page}}" data-page="{{$page}}">{{$page}}</a></li>
                @endif
            @endforeach
        @endif
        @if (!empty($pageLastDot))
            <li><a href="###" data-page="{{$pageLastDot}}">{{$pageLastDot}}</a></li>
        @endif
        @if (!empty($pageLast))
            <li><a href="{{$uri}}{{$pageLast}}" data-page="{{$pageLast}}">{{$pageLast}}</a></li>
        @endif
        <li><span>共 {{$pager['total']}} 条记录</span></li>
    </ul>
</div>

