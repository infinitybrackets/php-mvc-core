<?php

namespace InfinityBrackets\Core;
/*
 * This is not for sale but it's free to use for any web application
 *
 * @package    Infinity Brackets
 * @author     John Vincent Bonza
 * @copyright  2021 Infinity Brackets
 * @license    Free
 * @version    v3.0
 */
use InfinityBrackets\Core\Pagination;

class Pagination {
    public $paginationControls = "";
    public $maxControlsPerPage = 5;
    public $current = 1;
    public $last = 1;
    public $queryString = "";

    public function __construct($config = []) {
        if(array_key_exists('current', $config)) {
            $this->current = $config['current'];
        }
        if(array_key_exists('last', $config)) {
            $this->last = $config['last'];
        }
        if(array_key_exists('queryString', $config)) {
            $this->queryString .= $config['queryString'];
        }
        if(array_key_exists('orderBy', $config)) {
            $this->queryString .= '&orderBy=' . $config['orderBy'];
        }
    }
    
    public function GeneratePagination() {
        $this->queryString .='&page=';
        if($this->last != 1) {
            if ($this->current > 1) {
                // First Page
                if($this->current >= $this->maxControlsPerPage - 1) {
                    $this->paginationControls .= '<li class="paginate_button page-item"><a class="page-link" href="' . $this->queryString . '1"><span class="ib_pagination"><i class="fas fa-angle-double-left"></i></a></li>';
                }
                // Before Active Page
                for($i = $this->current - ($this->maxControlsPerPage - 1); $i < $this->current; $i++) {
                    if($i > 0) {
                        if($this->current - 3 < $i) {
                            $this->paginationControls .= '<li class="paginate_button page-item"><a class="page-link" href="' . $this->queryString . $i . '"><span class="ib_pagination">' . $i . '</a></li>';
                        }
                    }
                }
            }
            // Active Page
            $this->paginationControls .= '<li class="paginate_button page-item active"><a class="page-link" href="javascript:void(0)">' . $this->current . '</a></li>';
            // After Active Page
            for ($i = $this->current + 1; $i <= $this->last; $i++){
                $this->paginationControls .= '<li class="paginate_button page-item"><a class="page-link" href="' . $this->queryString . $i . '">' . $i . '</a></li>';
                if($i >= $this->current + 2) {
                    break;
                }
            }
            // this->last Page
            if($this->last >= $this->current + 3) {
                $this->paginationControls .= '<li class="paginate_button page-item"><a class="page-link" href="' . $this->queryString . $this->last . '"><span class="ib_pagination"><i class="fas fa-angle-double-right"></i></a></li>';
            }
        }
        return $this;
    }

    public function Render() {
        return $this->paginationControls;
    }

    public function Paginate($total, $limit, $options = []) {
        $last = ceil($total/$limit);
        if($last < 1){
            $last = 1;
        }

        // Establish the $pagenum variable
        $page = 1;
        $link = '?page=';

        // Configure options
        if($options) {
            // querystring
            if(array_key_exists('querystring', $options)) {
                $link = $options['querystring'] . '&page=';
            }
            //page
            if(array_key_exists('page', $options)) {
                $page = $options['page'];
            }
        }

        // Get page from URL vars if it is present, else it is = 1
        if(isset($request['page'])) {
            $page = preg_replace('#[^0-9]#', '', $request['page']);
        }
        if ($page < 1) { 
            $page = 1; 
        } else if ($page > $last) { 
            $page = $last; 
        }

        $pages = [];
        $current = $page;

        if($last != 1) {
            // Previous button
            if($page == 1) {
                $pages[] = [
                    'page' => NULL,
                    'link' => NULL,
                    'type' => 'previous'
                ];
            } else {
                $pages[] = [
                    'page' => $page - 1,
                    'link' => $link . ($page - 1),
                    'type' => 'previous'
                ];
            }
            if ($current > 1) {
                // Before Active Page
                for($i = $current - ($this->maxControlsPerPage - 1); $i < $current; $i++) {
                    if($i > 0) {
                        if($current - 4 < $i) {
                            $pages[] = [
                                'page' => $i,
                                'link' => $link . $i,
                                'type' => 'default'
                            ];
                        }
                    }
                }
            }

            // Current page
            $pages[] = [
                'page' => $page,
                'link' => $link . $page,
                'type' => 'active'
            ];
            
            // After Active Page
            for ($i = $current + 1; $i <= $last; $i++){
                $pages[] = [
                    'page' => $i,
                    'link' => $link . $i,
                    'type' => 'default'
                ];
                if($i >= $current + 3) {
                    break;
                }
            }

            // Next button
            if($page == $last) {
                $pages[] = [
                    'page' => NULL,
                    'link' => NULL,
                    'type' => 'next'
                ];
            } else {
                $pages[] = [
                    'page' => $page + 1,
                    'link' => $link . ($page + 1),
                    'type' => 'next'
                ];
            }
        }

        // Pagination data from
        $start = ($page * $limit) - $limit + 1;
        $end = $last == $page ? ($page * $limit) - $limit + $total : $page * $limit;

        return Application::$app->ToObject(['pages' => $pages, 'start' => $start, 'end' => $end]);
    }
}