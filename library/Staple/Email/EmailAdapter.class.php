<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 4/28/2016
 * Time: 1:47 PM
 */

namespace Stripe\Email;


interface EmailAdapter
{
    public function send();
    public function addTo($to);
    public function setTo($to);
    public function getTo();
    public function addCc($cc);
    public function getCc();
    public function addBcc($bcc);
    public function getBcc();
    public function setFrom($from,$name=NULL);
    public function getFrom();
    public function setSubject($subject);
    public function getSubject();
    public function setBody($body);
    public function getBody();
    public function addAttachment($attachment,$filename=NULL);
    public function setAttachments(array $attachments);
    public function getAttachments();
}