<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FixPasswordHashing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix unhashed passwords in the database by hashing them with bcrypt';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando usuarios con contraseñas sin hashear...');

        $users = User::all();
        $fixed = 0;
        $alreadyHashed = 0;

        foreach ($users as $user) {
            // Verificar si la contraseña ya está hasheada con bcrypt
            // Las contraseñas bcrypt empiezan con $2y$ y tienen 60 caracteres
            if (strlen($user->password) === 60 && str_starts_with($user->password, '$2y$')) {
                $alreadyHashed++;
                continue;
            }

            // La contraseña no está hasheada, la hasheamos
            $this->warn("Usuario {$user->email} tiene contraseña sin hashear");
            
            // Hashear la contraseña actual
            $user->password = Hash::make($user->password);
            $user->save();
            
            $fixed++;
            $this->info("✓ Contraseña hasheada para {$user->email}");
        }

        $this->newLine();
        $this->info("Proceso completado:");
        $this->info("- Usuarios con contraseñas ya hasheadas: {$alreadyHashed}");
        $this->info("- Usuarios con contraseñas arregladas: {$fixed}");
        
        if ($fixed > 0) {
            $this->newLine();
            $this->warn('IMPORTANTE: Las contraseñas sin hashear se han hasheado usando su valor actual.');
            $this->warn('Si necesitas que los usuarios tengan contraseñas específicas, actualízalas manualmente.');
        }

        return Command::SUCCESS;
    }
}
