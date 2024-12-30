<?php
    function isImagePortrait($image){
        list($width, $height) = getimagesize($image);
        if ($width > $height) {
            return false;
        } else {
            return true;
        }
    }

    function createImage($filepath){
        $imageExtension = getFileExtension($filepath);

        $image = "";
        if($imageExtension === "png"){
            $image = imagecreatefrompng($filepath);
        }
        else if($imageExtension === "jpeg" || $imageExtension === "jpg" || $imageExtension === "jfif"){
            $image = imagecreatefromjpeg($filepath);
        }

        return $image;
    }

    function getFileExtension($filePath) {
        // Parse the URI to extract the path
        $parsedUrl = parse_url($filePath);
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
    
        // Get the basename of the path
        $fileName = basename($path);
    
        // Get the extension of the file
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    
        return $fileExtension;
    }

    function rotateImage90($imagepath){
        $image = createImage($imagepath);

        $img = imagerotate($image, 90, 0);

        return $img;
    }

    function convertToPortrait($imagepath){
        if(!isImagePortrait($imagepath)){
            rotateImage90($imagepath);
        }
    }

    function saveToAttachments($image, $filepath){
        // Determine the image format
        $imageInfo = getimagesize($filepath); // Replace 'image.jpg' with the path to your image
        $imageMimeType = $imageInfo['mime'];

        // Save the image to a file based on its format
        switch ($imageMimeType) {
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/jfif':
                imagejpeg($image, $filepath);
                break;
            case 'image/png':
                imagepng($image, $filepath);
                break;
            default:
                // Unsupported format
                die('Unsupported image format');
        }
    }

    function parseToPortrait($filepath){
        if(!isImagePortrait($filepath)){
            $newImageObject = rotateImage90($filepath);

            saveToAttachments($newImageObject, $filepath);
        }   
    }
?>