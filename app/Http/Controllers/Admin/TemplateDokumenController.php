<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateDokumen;
use App\Models\TemplateDokumenField;
use App\Models\PejabatPenandatangan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TemplateDokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TemplateDokumen::with(['pejabat1', 'pejabat2']);

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $templates = $query->orderBy('kategori')->orderBy('nama')->paginate(15)->withQueryString();

        $stats = [
            'total' => TemplateDokumen::count(),
            'aktif' => TemplateDokumen::where('aktif', true)->count(),
            'akademik' => TemplateDokumen::where('kategori', 'akademik')->count(),
            'keuangan' => TemplateDokumen::where('kategori', 'keuangan')->count(),
        ];

        $kategoris = TemplateDokumen::KATEGORI;
        $pejabats = PejabatPenandatangan::where('aktif', true)->orderBy('urutan')->get();

        return view('admin.template-dokumen.index', compact('templates', 'stats', 'kategoris', 'pejabats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:template_dokumen,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', Rule::in(array_keys(TemplateDokumen::KATEGORI))],
            'deskripsi' => ['nullable', 'string'],
            'template_judul' => ['nullable', 'string'],
            'template_nomor' => ['nullable', 'string'],
            'template_isi' => ['nullable', 'string'],
            'template_penutup' => ['nullable', 'string'],
            'tampilkan_kop' => ['boolean'],
            'tampilkan_logo' => ['boolean'],
            'pejabat_1_id' => ['nullable', 'exists:pejabat_penandatangan,id'],
            'pejabat_1_label' => ['nullable', 'string', 'max:100'],
            'pejabat_2_id' => ['nullable', 'exists:pejabat_penandatangan,id'],
            'pejabat_2_label' => ['nullable', 'string', 'max:100'],
            'tampilkan_ttd_digital' => ['boolean'],
            'tampilkan_stempel' => ['boolean'],
            'catatan_bawah' => ['nullable', 'string'],
            'aktif' => ['boolean'],
        ]);

        $validated['tampilkan_kop'] = $request->has('tampilkan_kop');
        $validated['tampilkan_logo'] = $request->has('tampilkan_logo');
        $validated['tampilkan_ttd_digital'] = $request->has('tampilkan_ttd_digital');
        $validated['tampilkan_stempel'] = $request->has('tampilkan_stempel');
        $validated['aktif'] = $request->has('aktif');

        TemplateDokumen::create($validated);

        return redirect()->route('admin.template-dokumen.index')
            ->with('success', 'Template dokumen berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TemplateDokumen $templateDokumen)
    {
        $templateDokumen->load(['fields', 'pejabat1', 'pejabat2']);
        $kategoris = TemplateDokumen::KATEGORI;
        $placeholders = $templateDokumen->getAvailablePlaceholders();
        
        return view('admin.template-dokumen.show', compact('templateDokumen', 'kategoris', 'placeholders'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TemplateDokumen $templateDokumen)
    {
        $templateDokumen->load('fields');
        $kategoris = TemplateDokumen::KATEGORI;
        $pejabats = PejabatPenandatangan::where('aktif', true)->orderBy('urutan')->get();
        $tipeFields = TemplateDokumenField::TIPE;
        $sumberData = TemplateDokumenField::SUMBER_DATA;
        
        return view('admin.template-dokumen.edit', compact('templateDokumen', 'kategoris', 'pejabats', 'tipeFields', 'sumberData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TemplateDokumen $templateDokumen)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', Rule::unique('template_dokumen', 'kode')->ignore($templateDokumen->id)],
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', Rule::in(array_keys(TemplateDokumen::KATEGORI))],
            'deskripsi' => ['nullable', 'string'],
            'template_judul' => ['nullable', 'string'],
            'template_nomor' => ['nullable', 'string'],
            'template_isi' => ['nullable', 'string'],
            'template_penutup' => ['nullable', 'string'],
            'tampilkan_kop' => ['boolean'],
            'tampilkan_logo' => ['boolean'],
            'pejabat_1_id' => ['nullable', 'exists:pejabat_penandatangan,id'],
            'pejabat_1_label' => ['nullable', 'string', 'max:100'],
            'pejabat_2_id' => ['nullable', 'exists:pejabat_penandatangan,id'],
            'pejabat_2_label' => ['nullable', 'string', 'max:100'],
            'tampilkan_ttd_digital' => ['boolean'],
            'tampilkan_stempel' => ['boolean'],
            'catatan_bawah' => ['nullable', 'string'],
            'aktif' => ['boolean'],
        ]);

        $validated['tampilkan_kop'] = $request->has('tampilkan_kop');
        $validated['tampilkan_logo'] = $request->has('tampilkan_logo');
        $validated['tampilkan_ttd_digital'] = $request->has('tampilkan_ttd_digital');
        $validated['tampilkan_stempel'] = $request->has('tampilkan_stempel');
        $validated['aktif'] = $request->has('aktif');

        $templateDokumen->update($validated);

        return redirect()->route('admin.template-dokumen.index')
            ->with('success', 'Template dokumen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemplateDokumen $templateDokumen)
    {
        $templateDokumen->delete();

        return redirect()->route('admin.template-dokumen.index')
            ->with('success', 'Template dokumen berhasil dihapus.');
    }

    /**
     * Manage fields for a template
     */
    public function fields(TemplateDokumen $templateDokumen)
    {
        $templateDokumen->load('fields');
        $tipeFields = TemplateDokumenField::TIPE;
        $sumberData = TemplateDokumenField::SUMBER_DATA;
        
        return view('admin.template-dokumen.fields', compact('templateDokumen', 'tipeFields', 'sumberData'));
    }

    /**
     * Store a new field
     */
    public function storeField(Request $request, TemplateDokumen $templateDokumen)
    {
        $validated = $request->validate([
            'kode_field' => ['required', 'string', 'max:50', Rule::unique('template_dokumen_fields')->where(fn ($q) => $q->where('template_dokumen_id', $templateDokumen->id))],
            'label' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', Rule::in(array_keys(TemplateDokumenField::TIPE))],
            'opsi' => ['nullable', 'string'],
            'nilai_default' => ['nullable', 'string'],
            'sumber_data' => ['nullable', 'string'],
            'wajib' => ['boolean'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['template_dokumen_id'] = $templateDokumen->id;
        $validated['wajib'] = $request->has('wajib');
        $validated['urutan'] = $validated['urutan'] ?? 0;
        
        // Parse opsi jika ada (format: key1:Label 1, key2:Label 2)
        if (!empty($validated['opsi'])) {
            $opsiArray = [];
            $lines = explode("\n", $validated['opsi']);
            foreach ($lines as $line) {
                $line = trim($line);
                if (strpos($line, ':') !== false) {
                    [$key, $label] = explode(':', $line, 2);
                    $opsiArray[trim($key)] = trim($label);
                } else {
                    $opsiArray[$line] = $line;
                }
            }
            $validated['opsi'] = $opsiArray;
        } else {
            $validated['opsi'] = null;
        }

        TemplateDokumenField::create($validated);

        return redirect()->route('admin.template-dokumen.fields', $templateDokumen->hashid)
            ->with('success', 'Field berhasil ditambahkan.');
    }

    /**
     * Update a field
     */
    public function updateField(Request $request, TemplateDokumen $templateDokumen, TemplateDokumenField $field)
    {
        $validated = $request->validate([
            'kode_field' => ['required', 'string', 'max:50', Rule::unique('template_dokumen_fields')->where(fn ($q) => $q->where('template_dokumen_id', $templateDokumen->id))->ignore($field->id)],
            'label' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', Rule::in(array_keys(TemplateDokumenField::TIPE))],
            'opsi' => ['nullable', 'string'],
            'nilai_default' => ['nullable', 'string'],
            'sumber_data' => ['nullable', 'string'],
            'wajib' => ['boolean'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['wajib'] = $request->has('wajib');
        
        // Parse opsi
        if (!empty($validated['opsi'])) {
            $opsiArray = [];
            $lines = explode("\n", $validated['opsi']);
            foreach ($lines as $line) {
                $line = trim($line);
                if (strpos($line, ':') !== false) {
                    [$key, $label] = explode(':', $line, 2);
                    $opsiArray[trim($key)] = trim($label);
                } else {
                    $opsiArray[$line] = $line;
                }
            }
            $validated['opsi'] = $opsiArray;
        } else {
            $validated['opsi'] = null;
        }

        $field->update($validated);

        return redirect()->route('admin.template-dokumen.fields', $templateDokumen->hashid)
            ->with('success', 'Field berhasil diperbarui.');
    }

    /**
     * Delete a field
     */
    public function destroyField(TemplateDokumen $templateDokumen, TemplateDokumenField $field)
    {
        $field->delete();

        return redirect()->route('admin.template-dokumen.fields', $templateDokumen->hashid)
            ->with('success', 'Field berhasil dihapus.');
    }

    /**
     * Preview template dengan contoh data
     */
    public function preview(Request $request, TemplateDokumen $templateDokumen)
    {
        $templateDokumen->load(['fields', 'pejabat1', 'pejabat2']);
        
        // Contoh data untuk preview
        $data = [
            'nama' => 'Ahmad Fauzi',
            'nim' => '2023101001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '15 Januari 2000',
            'alamat' => 'Jl. Merdeka No. 123, Jakarta',
            'program_studi' => 'Teknik Informatika',
            'fakultas' => 'Fakultas Teknologi Industri',
            'angkatan' => '2023',
            'semester' => '3',
            'ipk' => '3.75',
            'no_surat' => '001',
        ];

        // Override dengan data dari request jika ada
        if ($request->filled('fields')) {
            $data = array_merge($data, $request->input('fields'));
        }

        $rendered = $templateDokumen->render($data);
        
        return view('admin.template-dokumen.preview', compact('templateDokumen', 'rendered', 'data'));
    }

    /**
     * Duplicate template
     */
    public function duplicate(TemplateDokumen $templateDokumen)
    {
        $new = $templateDokumen->replicate();
        $new->kode = $templateDokumen->kode . '_copy';
        $new->nama = $templateDokumen->nama . ' (Salinan)';
        $new->save();

        // Duplicate fields
        foreach ($templateDokumen->fields as $field) {
            $newField = $field->replicate();
            $newField->template_dokumen_id = $new->id;
            $newField->save();
        }

        return redirect()->route('admin.template-dokumen.edit', $new->hashid)
            ->with('success', 'Template berhasil diduplikasi.');
    }
}
