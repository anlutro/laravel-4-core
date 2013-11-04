<?php
namespace c\Auth\Activation;

interface ActivationCodeRepositoryInterface
{
	public function create(ActivatableInterface $user, $code);
	public function retrieveByCode($code);
	public function delete($code);
}
