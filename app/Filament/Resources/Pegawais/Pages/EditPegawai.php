use App\Filament\Resources\Pegawais\PegawaiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPegawai extends EditRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // 1. Ambil data nama dan email dari array 'user' yang dikirim oleh form
        $userData = $data['user'] ?? [];
        
        // 2. Jika pegawai punya user terkait, update datanya
        if ($record->user) {
            $record->user->update([
                'name' => $userData['name'] ?? $record->user->name,
                'email' => $userData['email'] ?? $record->user->email,
            ]);
        }

        // 3. Buang array 'user' dari $data supaya tidak error DB
        unset($data['user']);

        // 4. Update record Pegawai
        $record->update($data);

        return $record;
    }
}
