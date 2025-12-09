<?php
// Helpers simples para paginación y renderizado
function getPaginationParams($defaultPerPage = 10) {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
    $perPage = $defaultPerPage;
    $offset = ($page - 1) * $perPage;
    return ['q' => $q, 'page' => $page, 'perPage' => $perPage, 'offset' => $offset];
}

function renderPagination($total, $perPage, $page, $extraParams = []) {
    $pages = max(1, (int)ceil($total / $perPage));
    if ($pages <= 1) return ''; 

    $params = array_merge($_GET, $extraParams);
    unset($params['page']);
    $base = strtok($_SERVER['REQUEST_URI'], '?');
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    $prevClass = $page <= 1 ? ' disabled' : '';
    $params['page'] = $page - 1;
    $html .= '<li class="page-item' . $prevClass . '"><a class="page-link" href="' . htmlspecialchars($base . '?' . http_build_query($params)) . '">Anterior</a></li>';

  
    $start = max(1, $page - 3);
    $end = min($pages, $page + 3);
    if ($start > 1) {
        $params['page'] = 1;
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($base . '?' . http_build_query($params)) . '">1</a></li>';
        if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $params['page'] = $i;
        $active = $i === $page ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . htmlspecialchars($base . '?' . http_build_query($params)) . '">' . $i . '</a></li>';
    }

    if ($end < $pages) {
        if ($end < $pages - 1) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        $params['page'] = $pages;
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($base . '?' . http_build_query($params)) . '">' . $pages . '</a></li>';
    }

    $nextClass = $page >= $pages ? ' disabled' : '';
    $params['page'] = $page + 1;
    $html .= '<li class="page-item' . $nextClass . '"><a class="page-link" href="' . htmlspecialchars($base . '?' . http_build_query($params)) . '">Siguiente</a></li>';

    $html .= '</ul></nav>';
    return $html;
}

?>
