<?php
    function changeUriFolder($uri, $newFolder) {
        // Parse the URI
        $parsedUrl = parse_url($uri);
    
        // Extract the scheme and host from the original URI
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
    
        // Extract the path from the original URI
        $pathParts = explode('/', ltrim($parsedUrl['path'], '/'));
        $pathParts[0] = $newFolder; // Replace the first path segment with the new folder
        $newPath = '/' . implode('/', $pathParts);
    
        // Combine the new folder with the original query and fragment
        $newUri = $scheme . $host . $newPath;
    
        // Add query and fragment, if they exist
        if (isset($parsedUrl['query'])) {
            $newUri .= '?' . $parsedUrl['query'];
        }
        if (isset($parsedUrl['fragment'])) {
            $newUri .= '#' . $parsedUrl['fragment'];
        }
    
        return $newUri;
    }

    $mainUri = "https://www.example.com/path/to/resource";
    $newFolder = "Test";

    var_dump(changeUriFolder($mainUri, $newFolder));
?>