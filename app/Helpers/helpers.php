<?php

/**
 * Helper Function - included in Autoload
 */


if( !function_exists('showErrorMessage') )
{
    function showErrorMessage($debugMode = false, $message = '')
    {
        $errorMessage = $debugMode ? $message : 'An error occured';
        return $errorMessage;
    }
}

if (! function_exists('uploadFile')) {
    /**
     * Generate a random filename and move the uploaded file to the public/uploads folder.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string  The path of the stored file
     */
    function uploadFile($file)
    {
        // Generate a random name for the file
        $randomFileName = \Str::random(40) . '.' . date('Y-m-d') . '.' . $file->getClientOriginalExtension();

        // Move the file to the public/uploads directory
        $file->move(public_path('uploads'), $randomFileName);

        // Return the file path
        return 'uploads/' . $randomFileName;
    }
}

if (! function_exists('uploadMultipleFiles')) {
    /**
     * Handle uploading of multiple files.
     *
     * @param  \Illuminate\Http\UploadedFile[]  $files
     * @return array  An array with the paths of the uploaded files
     */
    function uploadMultipleFiles($files)
    {
        if (empty($files)) {
            return false;
        }

        $filePaths = [];
        foreach ($files as $file) {
            $filePaths[] = uploadFile($file);
        }
        return $filePaths;
    }
}
