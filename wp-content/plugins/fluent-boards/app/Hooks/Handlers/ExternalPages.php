<?php

namespace FluentBoards\App\Hooks\Handlers;

use FluentBoards\App\Models\Attachment;
use FluentBoards\App\App;
use FluentBoards\App\Models\CommentImage;
use FluentBoards\App\Models\Meta;
use FluentBoards\App\Services\Constant;
use FluentBoards\App\Services\Libs\FileSystem;

class ExternalPages
{
    public function view_uploaded_comment_image()
    {
        $attachmentHash = sanitize_text_field($_REQUEST['fbs_comment_image']);
        $boardId  = sanitize_text_field($_REQUEST['fbs_bid']);

        if (empty($attachmentHash)) {
            die('Invalid Attachment Hash');
        }

        $attachment = $this->getUploadedImageByHash($attachmentHash);

        if (!$attachment) {
            die('Invalid Attachment Hash');
        }

        $fileName = $attachment->file_path;
        $boardId = $boardId;
        $filePath = FileSystem::setSubDir('board_' . $boardId)->getDir() . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($filePath)) {
            die('File could not be found.');
        }

        $this->serveLocalAttachment($attachment, $filePath);
    }

    public function view_comment_image()
    {
        $attachmentHash = sanitize_text_field($_REQUEST['fbs_comment_image']);

        if (empty($attachmentHash)) {
            die('Invalid Attachment Hash');
        }

        $attachment = $this->getUploadedImageByHash($attachmentHash);

        if (in_array($attachment->object_type, [Constant::TASK_DESCRIPTION, Constant::COMMENT_IMAGE])) {
            $attachment->load('comment');
        }

        if (!$attachment) {
            die('Invalid Attachment Hash');
        }

        // check signature hash
        if (!$this->validateAttachmentSignature($attachment)) {
            $dieMessage = __('Sorry, Your secure sign is invalid, Please reload the previous page and get new signed url', 'fluent-support');
            die($dieMessage);
        }

        //If external file
        if ('local' !== $attachment->driver) {
            if(!empty($attachment->file_path)){
                $this->redirectToExternalAttachment($attachment->full_url);
            }else{
                die('File could not be found');
            }
        }

        //Handle Local file
        if (in_array($attachment->object_type, [Constant::TASK_DESCRIPTION, Constant::COMMENT_IMAGE])) {
            $fileName = $attachment->file_path;
            $boardId = $attachment->comment->board_id;
        }

        $filePath = FileSystem::setSubDir('board_' . $boardId)->getDir() . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($filePath)) {
            die('File could not be found.');
        }

        $this->serveLocalAttachment($attachment, $filePath);
    }

    private function getUploadedImageByHash($attachmentHash)
    {
        return CommentImage::where('file_hash', $attachmentHash)->first();
    }

    private function serveLocalAttachment($attachment, $filePath)
    {
        ob_get_clean();
        header("Content-Type: {$attachment->attachment_type}");
        header("Content-Disposition: inline; filename=\"{$attachment->title}\"");;
        echo readfile($filePath);
        die();
    }

    private function validateAttachmentSignature($attachment)
    {
        $sign = md5($attachment->id . date('YmdH'));
        return $sign === $_REQUEST['secure_sign'];
    }
}