<?php

namespace application\classes\pagination;

if(!class_exists('Pagination')):

    class Pagination
    {
        //////////////////////////////////////// Pagination options ////////////////////////////////////////
        protected $totalRows;
        protected $limit;
        protected $pageNumber;

        private $paginationOpts = array(
            'linkCount' => 8
        );
        //////////////////////////// Create some parameters for pagination layout /////////////////////////

        public function __construct( $totalRows, $limit, $pageNumber )
        {
            $this->totalRows = $totalRows;
            $this->limit = $limit;
            $this->pageNumber = $pageNumber;
        }

        private function getPaginationParams($pageNumber, $totalPages)
        {
            // Get linkCount from options variable
            $linkCount = $this->paginationOpts['linkCount'];
            
            $linkRange = ceil($linkCount / 2);
            
            // Determine how many links to show before and after the current page,
            // depending on total number of pages and current page position.
            if($linkCount > 0):
                
                // the number of page links before the current page
                $linksBefore = ($pageNumber <= $linkRange) ? ($pageNumber - 1) : $linkRange;
                
                // the number of page links after the current page
                $linksAfter = ($pageNumber <= ($totalPages - $linkRange)) ? $linkRange : ($totalPages - $pageNumber);
                
                // If links before is less than half of the link count: add the difference to the links after.
                if($linksBefore < $linkRange):
                    
                    // specify the number of links to add extra after the current page
                    $spareLinkSpots = $linkRange - $linksBefore;
                
                    // if adding the extra links after doesn't result in exceeding the total number of pages 
                    if(($pageNumber + $linksAfter + $spareLinkSpots) <= $totalPages)
                        $linksAfter = ($linksAfter + $spareLinkSpots);
                    
                    // else draw all pages after the current page
                    else 
                        $linksAfter = $totalPages - $pageNumber;
                endif;
                
                // If links after is less than half of the link count: add the difference to the links before.
                if($linksAfter < $linkRange):
                    
                    // specify the number of links to add extra before the current page
                    $spareLinkSpots = $linkRange - $linksAfter;
                    
                    // if adding the extra links before doesn't result in exceeding minimum page number of 1
                    if($pageNumber - $linksBefore - $spareLinkSpots >= 1)
                        $linksBefore = $linksBefore + $spareLinkSpots;
                    
                    // else draw all the pages before the current page
                    else
                        $linksBefore = $pageNumber - 1;
                endif;
            
                // Booleans: Determine wether we should draw first and last page links,
                // depending on if the range of the page links reaches the start and end of the pagination 
                $drawFirstPage = $pageNumber - $linksBefore > 1;
                $drawLastPage = $pageNumber + $linksAfter < $totalPages;
                
                // Create array with data to return to createPagination
                $paginationParams = array (
                    'linksBefore'   => $linksBefore,
                    'linksAfter'    => $linksAfter,
                    'drawFirstPage' => $drawFirstPage,
                    'drawLastPage'  => $drawLastPage
                );                
            endif;
            
            return $paginationParams;
        }
        
        public function createPagination ()
        {
            // Determine total number of pages
            $totalPages = $lastPage = ceil($this->totalRows / $this->limit);
            //////////////////
            if(isset($_POST['pagePropertiesSubmit'])):
                $pageNumber = 1;
            else:
                $pageNumber = $this->pageNumber;
            endif;
            // Ensure by override that page number is in range (between 1 and total number of pages)
            $pageNumber = max($pageNumber, 1);
            $pageNumber = min($pageNumber, $totalPages);
            // Get pagination parameters
            $paginationParams = $this->getPaginationParams($pageNumber, $totalPages);
            // Create markup string variable
            $markup = '<div class="pagination">';
            //////////////////// Draw pagination Intro ////////////////////  
            $markup .= '<div class="paginationFlex">';
            $markup .= '<div class="pagination-title">Page '.$pageNumber.' of '.$totalPages.'</div>';
            $markup .= '</div>';
            //////////////////// Draw urls ///////////////////////////////

            $data    = unserialize( CMS_GET_DATA );

            $allowed = ['sysLogFilterParams', 'sysLogFilter_sort', 'sysLogFilter_sortType'];

            if( !empty( $data ) ):
                foreach( $data as $key => $value ):
                    if( in_array( $key, $allowed ) ) ${$key} = $value;
                endforeach;
            endif;

            $sysLogFilterParams = ( !empty( $sysLogFilterParams ) ? $sysLogFilterParams : "" );
            $sysLogFilter_sort = ( !empty( $sysLogFilter_sort ) ? $sysLogFilter_sort : "" );
            $sysLogFilter_sortType = ( !empty( $sysLogFilter_sortType ) ? $sysLogFilter_sortType : "" );

            $urlData = array("sysLogFilterParams" => $sysLogFilterParams,
                             "limit" => $this->limit,
                             "totalRows" => $this->totalRows,
                             "sysLogFilter" => true,
                             "sysLogFilter_sort" => $sysLogFilter_sort,
                             "sysLogFilter_sortType" => $sysLogFilter_sortType
                             );

            //////////////////// Draw 'previous' link ////////////////////
            
            if($pageNumber > 1):
                $previous = $pageNumber - 1;
                $urlData['pageNumber'] = $previous;
                $markup .= '<div class="paginationFlex">';
                $markup .= '<div class="pagination-link">';
                $markup .= '<a href="'.LITENING_SELF.'?data='.base64_encode(json_encode($urlData)).'">Previous</a>';
                $markup .= '</div>';
                $markup .= '</div>';
            endif;
            
            
            //////////////////// Draw first page ////////////////////
            
            if($paginationParams['drawFirstPage']):
                $urlData['pageNumber'] = 1;
                $markup .= '<div class="paginationFlex">';
                $markup .= '<div class="pagination-link">';
                $markup .= '<a href="'.LITENING_SELF.'?data='.base64_encode(json_encode($urlData)).'">1...</a>';
                $markup .= '</div>';
                $markup .= '</div>';
            endif;
            
            
            //////////////////// Draw previous page sequence ////////////////////
            
            
            for($i = $pageNumber - $paginationParams['linksBefore']; $i < $pageNumber; $i++):
                $urlData['pageNumber'] = $i;
                $markup .= '<div class="paginationFlex">';
                $markup .= '<div class="pagination-link">';
                $markup .= '<a href="'.LITENING_SELF.'?data='.base64_encode(json_encode($urlData)).'">' . $i . '</a>';
                $markup .= '</div>';
                $markup .= '</div>';
            endfor;
            
            
            //////////////////// Draw current page ////////////////////
            
            $markup .= '<div class="paginationFlex">';
            $markup .= '<div class="pagination-current">';
            $markup .= $pageNumber;
            $markup .= '</div>';
            $markup .= '</div>';
            
            
            //////////////////// Draw next pages sequence ////////////////////
            
            
            for($i = $pageNumber+1, $l = $paginationParams['linksAfter']; $l > 0; $i++, $l--):
                $urlData['pageNumber'] = $i;
                $markup .= '<div class="paginationFlex">';
                $markup .= '<div class="pagination-link">';
                $markup .= '<a href="'.LITENING_SELF.'?data='.base64_encode(json_encode($urlData)).'">'.$i.'</a> ';
                $markup .= '</div>';
                $markup .= '</div>';
            endfor;
            
            
            //////////////////// Draw last page ////////////////////
            
            if($paginationParams['drawLastPage']):
                $urlData['pageNumber'] = $lastPage;
                $markup .= '<div class="paginationFlex">';
                $markup .= '<div class="pagination-link">';
                $markup .= '<a href="'.LITENING_SELF.'?data='.base64_encode(json_encode($urlData)).'">...'.$lastPage.'</a>';
                $markup .= '</div>';
                $markup .= '</div>';
            endif;
            
            
            //////////////////// Draw 'next' link ////////////////////
            
            if($pageNumber < $lastPage):
                $next = $pageNumber + 1;
                $urlData['pageNumber'] = $next;
                $markup .= '<div class="paginationFlex">';
                $markup .= '<div class="pagination-link">';
                $markup .= '<a href="'.LITENING_SELF.'?data='.base64_encode(json_encode($urlData)).'">Next</a>';
                $markup .= '</div>';
                $markup .= '</div>';
            endif;
            
            
            //////////////////// Finish and echo ////////////////////
            
            $markup .= '</div>';            
            return($markup); 
        }
    }
endif;
?>