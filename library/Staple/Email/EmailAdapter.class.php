<?php
/**
 * Email Adapter Interface
 *
 * @author Ironpilot
 * @copyright Copyright (c) 2011, STAPLE CODE
 *
 * This file is part of the STAPLE Framework.
 *
 * The STAPLE Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * The STAPLE Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the STAPLE Framework.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Staple\Email;


interface EmailAdapter
{
    public function send();
    public function addTo($to);
    public function setTo($to);
    public function getTo();
    public function addCc($cc);
	public function setCc($cc);
    public function getCc();
    public function addBcc($bcc);
	public function setBcc($bcc);
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