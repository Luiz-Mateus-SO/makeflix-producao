<?php

namespace App\Livewire;

use Livewire\Component;

class AdminLogin extends Component
{
    public string $password = '';

    protected function rules(): array
    {
        return [
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): mixed
    {
        $this->validate();

        $configuredPassword = (string) config('services.madeflix.admin_password');

        if ($configuredPassword === '' || ! hash_equals($configuredPassword, $this->password)) {
            $this->addError('password', 'Senha administrativa inválida.');
            $this->password = '';

            return null;
        }

        session()->put('madeflix_admin_authenticated', true);

        return redirect()->intended(route('create'));
    }

    public function render()
    {
        return view('livewire.admin-login');
    }
}
