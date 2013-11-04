<?php
namespace c\Auth\Reminders;

interface RemindableInterface
{
	public function getReminderEmail();
	public function setPasswordAttribute($password);
	public function save();
}
