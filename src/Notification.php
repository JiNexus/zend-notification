<?php
/**
 * This file is part of the JiNexus/Zend-Notification package.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed along with this source code.
 *
 *
 * @link      https://github.com/JiNexus/zend-notification
 * @copyright Copyright (c) 2017 Jimvirle Calago <jimvirle@gmail.com>
 * @license   https://github.com/JiNexus/zend-notification/license BSD 3-Clause License
 */

namespace JiNexus\Zend\Notification;

use Zend\Config\Config;
use Zend\Mail\Message;
use Zend\Mail\Transport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

class Notification
{
    const TYPE_HTML = 'html';
    const TYPE_TEXT = 'text';
    const TRANSPORT_SENDMAIL = 'sendmail';
    const TRANSPORT_SMTP = 'smtp';
    const TRANSPORT_FILE = 'file';
    const TRANSPORT_IN_MEMORY = 'inMemory';

    protected $attachments = [];
    protected $config = null;
    protected $content = null;
    protected $file = null;
    protected $fileTransportOptions = null;
    protected $footerData = [];
    protected $headerData = [];
    protected $html = null;
    protected $message = null;
    protected $sendmailTransportParameters = null;
    protected $smtpTransportOptions = null;
    protected $text = null;
    protected $template = null;
    protected $templateData = [];
    protected $transport = null;
    protected $type = Notification::TYPE_HTML;

    /**
     * Notification constructor.
     */
    public function __construct()
    {
        $this->message = new Message();
    }

    /**
     * Assemble the email template
     *
     * @return Notification
     */
    public function assemble()
    {
        $config = $this->getConfig()->toArray();
        if ($this->getTemplate() != null) {
            $config['notification']['template'] = $this->getTemplate();
        }

        $resolver = new TemplateMapResolver();
        $resolver->setMap($config['notification']);

        $view = new PhpRenderer();
        $view->setResolver($resolver);

        $footerViewModel = new ViewModel();
        $footerViewModel->setTemplate('footer');
        $footerViewModel->setVariables($this->getFooterData());

        $headerViewModel = new ViewModel();
        $headerViewModel->setTemplate('header');
        $headerViewModel->setVariables($this->getHeaderData());

        $templateViewModel  = new ViewModel();
        $templateViewModel->setTemplate('template');
        $templateViewModel->setVariables($this->getTemplateData());

        $layoutViewModel  = new ViewModel();
        $layoutViewModel->setTemplate('layout');
        $layoutViewModel->setVariables([
            'header' => $view->render($headerViewModel),
            'footer' => $view->render($footerViewModel),
            'template' => $view->render($templateViewModel)
        ]);

        $this->setContent($view->render($layoutViewModel));
        return $this;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param array $attachments
     * @return Notification
     */
    public function setAttachments(array $attachments = [])
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * Add a "Bcc" address
     *
     * @param  string|\Zend\Mail\Address|array|\Zend\Mail\AddressList|\Traversable $emailOrAddressOrList
     * @param  string|null $name
     * @return Notification
     */
    public function addBcc($emailOrAddressOrList, $name = null)
    {
        $this->message->addBcc($emailOrAddressOrList, $name);
        return $this;
    }

    /**
     * Retrieve list of BCC recipients
     *
     * @return \Zend\Mail\AddressList
     */
    public function getBcc()
    {
        return $this->message->getBcc();
    }

    /**
     * Set (overwrite) BCC addresses
     *
     * @param  string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable $emailOrAddressList
     * @param  string|null $name
     * @return Notification
     */
    public function setBcc($emailOrAddressList, $name = null)
    {
        $this->message->setBcc($emailOrAddressList, $name);
        return $this;
    }

    /**
     * Add a "Cc" address
     *
     * @param  string|\Zend\Mail\Address|array|\Zend\Mail\AddressList|\Traversable $emailOrAddressOrList
     * @param  string|null $name
     * @return Notification
     */
    public function addCc($emailOrAddressOrList, $name = null)
    {
        $this->message->addCc($emailOrAddressOrList, $name);
        return $this;
    }

    /**
     * Retrieve list of CC recipients
     *
     * @return \Zend\Mail\AddressList
     */
    public function getCc()
    {
        return $this->message->getCc();
    }

    /**
     * Set (overwrite) CC addresses
     *
     * @param  string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable $emailOrAddressList
     * @param  string|null $name
     * @return Notification
     */
    public function setCc($emailOrAddressList, $name = null)
    {
        $this->message->setCc($emailOrAddressList, $name);
        return $this;
    }

    /**
     * @return null|Config
     */
    public function getConfig()
    {
        if ($this->config == null) {
            $this->config = new Config(include __DIR__ . '/config/notification.global.php');
        }

        return $this->config;
    }

    /**
     * @param $config
     * @return Notification
     */
    public function setConfig(array $config = [])
    {
        $this->config = new Config($config);
        return $this;
    }

    /**
     * @return null
     * @throws Exception
     */
    public function getContent()
    {
        if ($this->content == null) {
            throw new Exception(
                sprintf(
                    'A man needs a content. A man must do what he feels needs to be done, even if it is dangerous or undesirable.'
                )
            );
        }

        return $this->content;
    }

    /**
     * @param null $content
     * @return Notification
     */
    public function setContent($content = null)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return array
     */
    public function getFooterData()
    {
        return $this->footerData;
    }

    /**
     * @param array $footerData
     * @return Notification
     */
    public function setFooterData(array $footerData = [])
    {
        $this->footerData = $footerData;
        return $this;
    }

    /**
     * Add a "From" address
     *
     * @param  string|\Zend\Mail\Address|array|\Zend\Mail\AddressList|\Traversable $emailOrAddressOrList
     * @param  string|null $name
     * @return Notification
     */
    public function addFrom($emailOrAddressOrList, $name = null)
    {
        $this->message->addFrom($emailOrAddressOrList, $name);
        return $this;
    }

    /**
     * Retrieve list of From senders
     *
     * @return \Zend\Mail\AddressList
     * @throws Exception
     */
    public function getFrom()
    {
        if ($this->message->getFrom()->count() == null || empty($this->message->getFrom()->count())) {
            throw new Exception(
                sprintf(
                    'A man needs a name (from). A man must do what he feels needs to be done, even if it is dangerous or undesirable.'
                )
            );
        }

        return $this->message->getFrom();
    }

    /**
     * Set (overwrite) From addresses
     *
     * @param  string|\Zend\Mail\Address|array|\Zend\Mail\AddressList|\Traversable $emailOrAddressList
     * @param  string|null $name
     * @return Notification
     */
    public function setFrom($emailOrAddressList, $name = null)
    {
        $this->message->setFrom($emailOrAddressList, $name);
        return $this;
    }

    /**
     * @param $file
     * @return null|MimePart
     */
    public function getFile($file)
    {
        $this->file = new MimePart(fopen($file, 'r'));
        $this->file->filename = basename($file);
        $this->file->disposition = Mime::DISPOSITION_ATTACHMENT;
        $this->file->encoding = Mime::ENCODING_BASE64;

        return $this->file;
    }

    /**
     * @param MimeMessage $body
     */
    public function getFileAttachment(MimeMessage $body)
    {
        if (! empty($this->getAttachments())) {
            foreach ($this->getAttachments() as $key => $attachment) {
                $body->addPart($this->getFile($attachment));
            }
        }
    }

    /**
     * @return null
     * @throws Exception
     */
    public function getFileTransportOptions()
    {
        if ($this->fileTransportOptions == null) {
            throw new Exception(
                sprintf(
                    'A man needs a file transport options. A man must do what he feels needs to be done, even if it is dangerous or undesirable.'
                )
            );
        }

        return $this->fileTransportOptions;
    }

    /**
     * @param array $fileTransportOptions
     * @return Notification
     */
    public function setFileTransportOptions(array $fileTransportOptions = [])
    {
        $this->fileTransportOptions = new Config($fileTransportOptions);
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaderData()
    {
        return $this->headerData;
    }

    /**
     * @param array $headerData
     * @return Notification
     */
    public function setHeaderData(array $headerData = [])
    {
        $this->headerData = $headerData;
        return $this;
    }

    /**
     * @return null|MimePart
     */
    public function getHtml()
    {
        $this->html = new MimePart($this->getContent());
        $this->html->type = Mime::TYPE_HTML;
        $this->html->charset = 'utf-8';
        $this->html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

        return $this->html;
    }

    /**
     * Check all message configuration.
     */
    public function messageCheck()
    {
        $this->getFrom();
        $this->getTo();
    }

    /**
     * Add one or more addresses to the Reply-To recipients
     *
     * Appends to the list.
     *
     * @param  string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable $emailOrAddressOrList
     * @param  null|string $name
     * @return Notification
     */
    public function addReplyTo($emailOrAddressOrList, $name = null)
    {
        $this->message->addReplyTo($emailOrAddressOrList, $name);
        return $this;
    }

    /**
     * Access the address list of the Reply-To header
     *
     * @return \Zend\Mail\AddressList
     */
    public function getReplyTo()
    {
        return $this->message->getReplyTo();
    }

    /**
     * Overwrite the address list in the Reply-To recipients
     *
     * @param  string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable $emailOrAddressList
     * @param  null|string $name
     * @return Notification
     */
    public function setReplyTo($emailOrAddressList, $name = null)
    {
        $this->message->setReplyTo($emailOrAddressList, $name);
        return $this;
    }

    /**
     * Retrieve the sender address, if any
     *
     * @return null|\Zend\Mail\Address\AddressInterface
     */
    public function getSender()
    {
        return $this->message->getSender();
    }

    /**
     * Set Sender
     *
     * @param mixed $emailOrAddress
     * @param mixed $name
     * @return Notification
     */
    public function setSender($emailOrAddress, $name = null)
    {
        $this->message->setSender($emailOrAddress, $name);
        return $this;
    }

    /**
     * @return null
     */
    public function getSendmailTransportParameters()
    {
        return $this->sendmailTransportParameters;
    }

    /**
     * @param  null|string|array|\Traversable $sendmailTransportParameters OPTIONAL (Default: null)
     * @return Notification
     */
    public function setSendmailTransportParameters(array $sendmailTransportParameters = [])
    {
        $this->sendmailTransportParameters = $sendmailTransportParameters;
        return $this;
    }

    /**
     * @return null
     * @throws Exception
     */
    public function getSmtpTransportOptions()
    {
        if ($this->smtpTransportOptions == null) {
            throw new Exception(
                sprintf(
                    'A man needs an smtp transport options. A man must do what he feels needs to be done, even if it is dangerous or undesirable.'
                )
            );
        }

        return $this->smtpTransportOptions;
    }

    /**
     * @param array $smtpTransportOptions
     * @return Notification
     */
    public function setSmtpTransportOptions(array $smtpTransportOptions = [])
    {
        $options = new Config($smtpTransportOptions);
        $this->smtpTransportOptions = $options->toArray();
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSubject()
    {
        return $this->message->getSubject();
    }

    /**
     * Set the message subject header value
     *
     * @param  string $subject
     * @return Notification
     */
    public function setSubject($subject)
    {
        $this->message->setSubject($subject);
        return $this;
    }

    /**
     * @return null
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param $template
     * @return Notification
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return array
     */
    public function getTemplateData()
    {
        return $this->templateData;
    }

    /**
     * @param array $templateData
     * @return Notification
     */
    public function setTemplateData(array $templateData = [])
    {
        $this->templateData = $templateData;
        return $this;
    }

    /**
     * @return null|MimePart
     */
    public function getText()
    {
        $this->text = new MimePart($this->getContent());
        $this->text->type = Mime::TYPE_TEXT;
        $this->text->charset = 'utf-8';
        $this->text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

        return $this->text;
    }

    /**
     * Add one or more addresses to the To recipients
     *
     * Appends to the list.
     *
     * @param  string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable $emailOrAddressOrList
     * @param  null|string $name
     * @return Notification
     */
    public function addTo($emailOrAddressOrList, $name = null)
    {
        $this->message->addTo($emailOrAddressOrList, $name);
        return $this;
    }

    /**
     * Access the address list of the To header
     *
     * @return \Zend\Mail\AddressList
     * @throws Exception
     */
    public function getTo()
    {
        if ($this->message->getTo()->count() == null || empty($this->message->getTo()->count())) {
            throw new Exception(
                sprintf(
                    'A man needs a recipient (to). A man must do what he feels needs to be done, even if it is dangerous or undesirable.'
                )
            );
        }

        return $this->message->getTo();
    }

    /**
     * Overwrite the address list in the To recipients
     *
     * @param  string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable $emailOrAddressList
     * @param  null|string $name
     * @return Notification
     */
    public function setTo($emailOrAddressList, $name = null)
    {
        $this->message->setTo($emailOrAddressList, $name);
        return $this;
    }

    /**
     * @return null
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param string $transport
     * @return Notification
     */
    public function setTransport($transport = Notification::TRANSPORT_SENDMAIL)
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Notification
     */
    public function setType($type = Notification::TYPE_HTML)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Send Mail
     *
     * @throws Exception
     */
    public function send()
    {
        $body = new MimeMessage();

        switch ($this->getType()) {
            case Notification::TYPE_TEXT:
                if (! empty($this->getAttachments())) {
                    $body->setParts([$this->getText()]);
                    $this->getFileAttachment($body);
                    $type = 'multipart/related';
                } else {
                    $body->setParts([$this->getText()]);
                }
                break;

            case Notification::TYPE_HTML:
            default:
                if (! empty($this->getAttachments())) {
                    $body->setParts([$this->getHtml()]);
                    $this->getFileAttachment($body);
                    $type = 'multipart/related';
                } else {
                    $body->setParts([$this->getHtml()]);
                }
                break;
        }

        $this->message->setBody($body);

        if (! empty($this->getAttachments())) {
            $contentTypeHeader = $this->message->getHeaders()->get('Content-Type');
            $contentTypeHeader->setType($type);
        }

        $this->messageCheck();
        if (! $this->message->isValid()) {
            throw new Exception(
                sprintf(
                    'Opps, A man missed his preparation. A man must do what he feels needs to be done, even if it is dangerous or undesirable.'
                )
            );
        }

        switch ($this->getTransport()) {
            case Notification::TRANSPORT_SMTP:
                $transport = new Transport\Smtp();
                $options = new Transport\SmtpOptions($this->getSmtpTransportOptions());
                $transport->setOptions($options);
                $transport->send($this->message);
                break;

            case Notification::TRANSPORT_FILE:
                $transport = new Transport\File();
                $options = new Transport\FileOptions($this->getFileTransportOptions());
                $transport->setOptions($options);
                $transport->send($this->message);
                break;

            case Notification::TRANSPORT_IN_MEMORY:
                $transport = new Transport\InMemory();
                $transport->send($this->message);
                break;

            case Notification::TRANSPORT_SENDMAIL:
            default:
                $transport = new Transport\Sendmail($this->getSendmailTransportParameters());
                $transport->send($this->message);
                break;
        }
    }
}