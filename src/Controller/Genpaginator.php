<?php
namespace App\Controller;

class Genpaginator
{
    public function generatePaging($totalResults, $resultsPerPage, $page, $url)
    {
        $url = preg_replace('/(\/.)$/', "", $url);
        //$url = str_replace("http:/", "http://", $url);
        $paging = "";
        # Get total page count
        $pages = ceil($totalResults / $resultsPerPage);
        # Don't show pagination if there's only one page
        if ($pages == 1) {
            return;
        }
        # Show back to first page if not first page
        if ($page != 1) {
            $paging = '<li class="footable-page-arrow page-item"><a href="'. $url . '/1"  aria-label="Previous" class="page-link"> <span aria-hidden="true" class="s7-angle-left"></span> </a></li>';
        }
        # Create a link for each page
        $pageCount = 1;
        $paging .= '<!--' . $pages . ' -->';
        while ($pageCount <= $pages) {
            $active = ($pageCount == $page) ? "active" : $page . "-" . $pageCount;
            $paging .= '<li class="footable-page page-item ' . $active . '" id="' . $pageCount . '"><a href="'. $url .'/'. $pageCount . '" class="page-link">' . $pageCount . '</a></li>';
            $pageCount++;
        }
        # Show go to last page option if not the last page
        if ($page != $pages) {
            $paging .= '<li class="footable-page-arrow page-item"><a href="'. $url . '/' . $pages . '" aria-label="Next" class="page-link"> <span aria-hidden="true" class="s7-angle-right"></span> </a></li>';
        }
        return $paging;
    }
}
