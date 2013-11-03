<?php
namespace c\Auth\Activation;

interface ActivationCodeRepositoryInterface
{
	public function retrieveByCode($code);
	public function delete($code);
}
