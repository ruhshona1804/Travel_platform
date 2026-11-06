<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** config/admin.php asosida ishlaydigan umumiy CRUD kontrolleri. */
class ResourceController extends Controller
{
    private function cfg(string $resource): array
    {
        return config("admin.$resource") ?? abort(404);
    }

    public function index(Request $request, string $resource)
    {
        $cfg = $this->cfg($resource);
        $q = $cfg['model']::query();
        if ($s = trim((string) $request->query('q'))) {
            $q->where(function ($w) use ($cfg, $s) {
                foreach ($cfg['columns'] as $col) {
                    [$name, $type] = array_pad(explode(':', $col), 2, null);
                    if ($type === 'relation') continue;
                    $w->orWhere($name, 'like', '%'.addcslashes($s, '%_\\').'%');
                }
            });
        }
        $q->orderByDesc($cfg['order'] ?? 'id');
        return view('admin.resource.index', ['resource' => $resource, 'cfg' => $cfg, 'rows' => $q->paginate(20)->withQueryString()]);
    }

    public function create(string $resource)
    {
        $cfg = $this->cfg($resource);
        return view('admin.resource.form', ['resource' => $resource, 'cfg' => $cfg, 'row' => new $cfg['model']]);
    }

    public function store(Request $request, string $resource)
    {
        $cfg = $this->cfg($resource);
        $row = new $cfg['model'];
        $row->forceFill($this->payload($request, $cfg, $row, null))->save();
        ActivityLog::record("create:$resource", (string) $row->getKey());
        return redirect()->route('admin.resource.index', $resource)->with('status', __('ui.saved'));
    }

    public function edit(string $resource, int $id)
    {
        $cfg = $this->cfg($resource);
        return view('admin.resource.form', ['resource' => $resource, 'cfg' => $cfg, 'row' => $cfg['model']::findOrFail($id)]);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $cfg = $this->cfg($resource);
        $row = $cfg['model']::findOrFail($id);
        $row->forceFill($this->payload($request, $cfg, $row, $id))->save();
        ActivityLog::record("update:$resource", (string) $row->getKey());
        return redirect()->route('admin.resource.index', $resource)->with('status', __('ui.saved'));
    }

    public function destroy(string $resource, int $id)
    {
        Gate::authorize('manage-system'); // faqat Admin / Super Admin o'chira oladi
        $cfg = $this->cfg($resource);
        $row = $cfg['model']::findOrFail($id);
        $row->delete();
        ActivityLog::record("delete:$resource", (string) $id);
        return redirect()->route('admin.resource.index', $resource)->with('status', __('ui.deleted'));
    }

    private function payload(Request $request, array $cfg, $row, ?int $id): array
    {
        $locales = array_keys(config('travel.locales'));
        $rules = [];
        foreach ($cfg['fields'] as $f) {
            $n = $f['name'];
            switch ($f['type']) {
                case 'tr': case 'trtext':
                    $rules[$n] = [! empty($f['required']) ? 'required' : 'nullable', 'array'];
                    $rules["$n.uz"] = [! empty($f['required']) ? 'required' : 'nullable', 'string', $f['type'] === 'tr' ? 'max:255' : 'max:20000'];
                    foreach ($locales as $l) if ($l !== 'uz') $rules["$n.$l"] = ['nullable', 'string', $f['type'] === 'tr' ? 'max:255' : 'max:20000'];
                    break;
                case 'image': $rules[$n] = 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:4096'; break;
                case 'images': $rules[$n] = 'nullable|array|max:20'; $rules["$n.*"] = 'image|mimes:jpg,jpeg,png,webp|max:4096'; break;
                case 'bool': $rules[$n] = 'nullable|boolean'; break;
                case 'select': $rules[$n] = ['required', Rule::in($f['options'])]; break;
                case 'multi': $rules[$n] = 'nullable|array'; $rules["$n.*"] = [Rule::in($f['options'])]; break;
                case 'csv': case 'kv': $rules[$n] = 'nullable|string|max:5000'; break;
                default:
                    $r = $f['rules'] ?? 'nullable|string|max:255';
                    if (! empty($f['unique'])) {
                        $r = (is_array($r) ? $r : explode('|', $r));
                        $r[] = Rule::unique($f['unique'], $n)->ignore($id);
                    }
                    $rules[$n] = $r;
            }
        }
        $data = $request->validate($rules);

        $out = [];
        foreach ($cfg['fields'] as $f) {
            $n = $f['name'];
            switch ($f['type']) {
                case 'tr': case 'trtext':
                    $out[$n] = collect($data[$n] ?? [])->map(fn ($v) => $v === null ? null : strip_tags((string) $v, $f['type'] === 'trtext' ? '<p><br><b><strong><i><em><ul><ol><li><h3><h4><a>' : ''))->filter(fn ($v) => $v !== null && $v !== '')->all();
                    break;
                case 'bool': $out[$n] = $request->boolean($n); break;
                case 'image':
                    if ($request->hasFile($n)) {
                        if ($row->$n && ! str_starts_with($row->$n, 'http')) Storage::disk('public')->delete($row->$n);
                        $out[$n] = $request->file($n)->store(\Illuminate\Support\Str::snake(class_basename($cfg['model'])), 'public');
                    } elseif ($request->boolean("remove_$n")) {
                        $out[$n] = null;
                    }
                    break;
                case 'images':
                    $keep = array_values(array_diff((array) ($row->$n ?? []), (array) $request->input("remove_$n", [])));
                    foreach ((array) $request->file($n, []) as $file) $keep[] = $file->store('gallery', 'public');
                    $out[$n] = $keep ?: null;
                    break;
                case 'multi':
                    $v = $data[$n] ?? [];
                    $out[$n] = ! empty($f['csv']) ? implode(',', $v) : $v;
                    break;
                case 'csv':
                    $out[$n] = array_values(array_filter(array_map('trim', explode(',', $data[$n] ?? ''))));
                    break;
                case 'kv':
                    $kv = [];
                    foreach (preg_split('/\R/', (string) ($data[$n] ?? '')) as $line) {
                        if (str_contains($line, '=')) { [$k, $v] = array_map('trim', explode('=', $line, 2)); if ($k !== '') $kv[$k] = $v; }
                    }
                    $out[$n] = $kv;
                    break;
                default:
                    $v = ($data[$n] ?? null) === '' ? null : ($data[$n] ?? null);
                    $out[$n] = $v ?? ($f['default'] ?? null);
            }
        }
        return $out;
    }
}
