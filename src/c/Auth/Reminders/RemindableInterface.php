<?php
namespace c\Auth\Reminders;

interface RemindableInterface
{
	public function getReminderEmail();
	public function setPasswordAttribute();
	public function save();
}
