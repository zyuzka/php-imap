<?php

namespace LaravelPhpIMAP;

class IncomingMail
{
    public $id;
    public $date;
    public $headersRaw;
    public $headers;
    public $subject;

    public $fromName;
    public $fromAddress;

    public $to = [];
    public $toString;
    public $cc = [];
    public $bcc = [];
    public $replyTo = [];

    public $messageId;

    public $textPlain;
    public $textHtml;

    protected $attachments = [];

    public function addAttachment(IncomingMailAttachment $attachment)
    {
        $this->attachments[$attachment->id] = $attachment;
    }

    /**
     * @return IncomingMailAttachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Get array of internal HTML links placeholders
     * @return array attachmentId => link placeholder
     */
    public function getInternalLinksPlaceholders()
    {
        return preg_match_all('/=["\'](ci?d:([\w\.%*@-]+))["\']/i', $this->textHtml, $matches) ? array_combine($matches[2], $matches[1]) : [];

    }

    public function replaceInternalLinks($baseUri)
    {
        $baseUri = rtrim($baseUri, '\\/') . '/';
        $fetchedHtml = $this->textHtml;
        foreach ($this->getInternalLinksPlaceholders() as $attachmentId => $placeholder) {
            if (isset($this->attachments[$attachmentId])) {
                $fetchedHtml = str_replace($placeholder, $baseUri . basename($this->attachments[$attachmentId]->filePath), $fetchedHtml);
            }
        }

        return $fetchedHtml;
    }
}
