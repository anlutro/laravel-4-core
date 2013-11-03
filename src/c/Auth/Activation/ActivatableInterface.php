<?php
namespace c\Auth\Activation;

interface ActivatableInterface
{
	public function activate();
	public function deactivate($newActivationCode = null);
}
