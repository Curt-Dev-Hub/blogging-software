<?php 
function base_url($path = '') {
    $base = '/blogging-software'; // project root
    return $base . '/' . ltrim($path, '/');
}