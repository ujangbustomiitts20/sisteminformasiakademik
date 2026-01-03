<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontenPmb;
use App\Models\SliderPmb;
use App\Models\FaqPmb;
use App\Models\TestimoniPmb;
use App\Models\GaleriPmb;
use App\Models\BeritaPmb;
use App\Models\KeunggulanPmb;
use App\Models\FasilitasPmb;
use App\Models\KontakPmb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KontenPmbController extends Controller
{
    /**
     * Dashboard kelola konten PMB
     */
    public function index()
    {
        return view('admin.konten-pmb.index', [
            'sliderCount' => SliderPmb::count(),
            'beritaCount' => BeritaPmb::count(),
            'faqCount' => FaqPmb::count(),
            'testimoniCount' => TestimoniPmb::count(),
            'galeriCount' => GaleriPmb::count(),
            'keunggulanCount' => KeunggulanPmb::count(),
            'fasilitasCount' => FasilitasPmb::count(),
            'kontakCount' => KontakPmb::count(),
        ]);
    }

    // ==================
    // SLIDER
    // ==================
    public function sliderIndex()
    {
        return view('admin.konten-pmb.slider.index', [
            'sliders' => SliderPmb::orderBy('urutan')->get()
        ]);
    }

    public function sliderCreate()
    {
        return view('admin.konten-pmb.slider.create');
    }

    public function sliderStore(Request $request)
    {
        $request->validate([
            'gambar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:50',
        ]);

        $data = $request->only(['judul', 'deskripsi', 'link', 'button_text']);
        $data['urutan'] = SliderPmb::max('urutan') + 1;
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('pmb/slider', 'public');
        }

        SliderPmb::create($data);

        return redirect()->route('admin.konten-pmb.slider.index')
            ->with('success', 'Slider berhasil ditambahkan');
    }

    public function sliderEdit(SliderPmb $slider)
    {
        return view('admin.konten-pmb.slider.edit', compact('slider'));
    }

    public function sliderUpdate(Request $request, SliderPmb $slider)
    {
        $request->validate([
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:50',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['judul', 'deskripsi', 'link', 'button_text', 'urutan']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('gambar')) {
            if ($slider->gambar) {
                Storage::disk('public')->delete($slider->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('pmb/slider', 'public');
        }

        $slider->update($data);

        return redirect()->route('admin.konten-pmb.slider.index')
            ->with('success', 'Slider berhasil diperbarui');
    }

    public function sliderDestroy(SliderPmb $slider)
    {
        if ($slider->gambar) {
            Storage::disk('public')->delete($slider->gambar);
        }
        $slider->delete();

        return redirect()->route('admin.konten-pmb.slider.index')
            ->with('success', 'Slider berhasil dihapus');
    }

    // ==================
    // BERITA
    // ==================
    public function beritaIndex()
    {
        return view('admin.konten-pmb.berita.index', [
            'berita' => BeritaPmb::latest()->paginate(10)
        ]);
    }

    public function beritaCreate()
    {
        return view('admin.konten-pmb.berita.create');
    }

    public function beritaStore(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'kategori' => 'required|in:berita,pengumuman,info',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['judul', 'konten', 'kategori', 'ringkasan']);
        $data['slug'] = Str::slug($request->judul);
        $data['user_id'] = auth()->id();
        $data['is_featured'] = $request->has('is_featured');
        $data['is_published'] = $request->has('is_published');
        
        if ($data['is_published']) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('pmb/berita', 'public');
        }

        BeritaPmb::create($data);

        return redirect()->route('admin.konten-pmb.berita.index')
            ->with('success', 'Berita berhasil ditambahkan');
    }

    public function beritaEdit(BeritaPmb $berita)
    {
        return view('admin.konten-pmb.berita.edit', compact('berita'));
    }

    public function beritaUpdate(Request $request, BeritaPmb $berita)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'kategori' => 'required|in:berita,pengumuman,info',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['judul', 'konten', 'kategori', 'ringkasan']);
        $data['is_featured'] = $request->has('is_featured');
        $data['is_published'] = $request->has('is_published');

        if ($data['is_published'] && !$berita->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('gambar')) {
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('pmb/berita', 'public');
        }

        $berita->update($data);

        return redirect()->route('admin.konten-pmb.berita.index')
            ->with('success', 'Berita berhasil diperbarui');
    }

    public function beritaDestroy(BeritaPmb $berita)
    {
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }
        $berita->delete();

        return redirect()->route('admin.konten-pmb.berita.index')
            ->with('success', 'Berita berhasil dihapus');
    }

    // ==================
    // FAQ
    // ==================
    public function faqIndex()
    {
        return view('admin.konten-pmb.faq.index', [
            'faqs' => FaqPmb::orderBy('urutan')->get()
        ]);
    }

    public function faqCreate()
    {
        return view('admin.konten-pmb.faq.create');
    }

    public function faqStore(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:500',
            'jawaban' => 'required|string',
            'kategori' => 'nullable|string|max:100',
        ]);

        FaqPmb::create([
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'kategori' => $request->kategori,
            'urutan' => FaqPmb::max('urutan') + 1,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.konten-pmb.faq.index')
            ->with('success', 'FAQ berhasil ditambahkan');
    }

    public function faqEdit(FaqPmb $faq)
    {
        return view('admin.konten-pmb.faq.edit', compact('faq'));
    }

    public function faqUpdate(Request $request, FaqPmb $faq)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:500',
            'jawaban' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $faq->update([
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'kategori' => $request->kategori,
            'urutan' => $request->urutan ?? $faq->urutan,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.konten-pmb.faq.index')
            ->with('success', 'FAQ berhasil diperbarui');
    }

    public function faqDestroy(FaqPmb $faq)
    {
        $faq->delete();
        return redirect()->route('admin.konten-pmb.faq.index')
            ->with('success', 'FAQ berhasil dihapus');
    }

    // ==================
    // TESTIMONI
    // ==================
    public function testimoniIndex()
    {
        return view('admin.konten-pmb.testimoni.index', [
            'testimonis' => TestimoniPmb::orderBy('urutan')->get()
        ]);
    }

    public function testimoniCreate()
    {
        return view('admin.konten-pmb.testimoni.create');
    }

    public function testimoniStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'testimoni' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        $data = $request->only(['nama', 'angkatan', 'program_studi', 'testimoni', 'pekerjaan']);
        $data['urutan'] = TestimoniPmb::max('urutan') + 1;
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('pmb/testimoni', 'public');
        }

        TestimoniPmb::create($data);

        return redirect()->route('admin.konten-pmb.testimoni.index')
            ->with('success', 'Testimoni berhasil ditambahkan');
    }

    public function testimoniEdit(TestimoniPmb $testimoni)
    {
        return view('admin.konten-pmb.testimoni.edit', compact('testimoni'));
    }

    public function testimoniUpdate(Request $request, TestimoniPmb $testimoni)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'testimoni' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['nama', 'angkatan', 'program_studi', 'testimoni', 'pekerjaan', 'urutan']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('foto')) {
            if ($testimoni->foto) {
                Storage::disk('public')->delete($testimoni->foto);
            }
            $data['foto'] = $request->file('foto')->store('pmb/testimoni', 'public');
        }

        $testimoni->update($data);

        return redirect()->route('admin.konten-pmb.testimoni.index')
            ->with('success', 'Testimoni berhasil diperbarui');
    }

    public function testimoniDestroy(TestimoniPmb $testimoni)
    {
        if ($testimoni->foto) {
            Storage::disk('public')->delete($testimoni->foto);
        }
        $testimoni->delete();

        return redirect()->route('admin.konten-pmb.testimoni.index')
            ->with('success', 'Testimoni berhasil dihapus');
    }

    // ==================
    // GALERI
    // ==================
    public function galeriIndex()
    {
        return view('admin.konten-pmb.galeri.index', [
            'galeris' => GaleriPmb::orderBy('urutan')->paginate(12)
        ]);
    }

    public function galeriCreate()
    {
        return view('admin.konten-pmb.galeri.create');
    }

    public function galeriStore(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori' => 'nullable|string|max:100',
        ]);

        $data = $request->only(['judul', 'deskripsi', 'kategori']);
        $data['urutan'] = GaleriPmb::max('urutan') + 1;
        $data['is_active'] = $request->has('is_active');
        $data['gambar'] = $request->file('gambar')->store('pmb/galeri', 'public');

        GaleriPmb::create($data);

        return redirect()->route('admin.konten-pmb.galeri.index')
            ->with('success', 'Galeri berhasil ditambahkan');
    }

    public function galeriEdit(GaleriPmb $galeri)
    {
        return view('admin.konten-pmb.galeri.edit', compact('galeri'));
    }

    public function galeriUpdate(Request $request, GaleriPmb $galeri)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['judul', 'deskripsi', 'kategori', 'urutan']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('gambar')) {
            if ($galeri->gambar) {
                Storage::disk('public')->delete($galeri->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('pmb/galeri', 'public');
        }

        $galeri->update($data);

        return redirect()->route('admin.konten-pmb.galeri.index')
            ->with('success', 'Galeri berhasil diperbarui');
    }

    public function galeriDestroy(GaleriPmb $galeri)
    {
        if ($galeri->gambar) {
            Storage::disk('public')->delete($galeri->gambar);
        }
        $galeri->delete();

        return redirect()->route('admin.konten-pmb.galeri.index')
            ->with('success', 'Galeri berhasil dihapus');
    }

    // ==================
    // KEUNGGULAN
    // ==================
    public function keunggulanIndex()
    {
        return view('admin.konten-pmb.keunggulan.index', [
            'keunggulans' => KeunggulanPmb::orderBy('urutan')->get()
        ]);
    }

    public function keunggulanCreate()
    {
        return view('admin.konten-pmb.keunggulan.create');
    }

    public function keunggulanStore(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['judul', 'deskripsi', 'icon']);
        $data['urutan'] = KeunggulanPmb::max('urutan') + 1;
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('pmb/keunggulan', 'public');
        }

        KeunggulanPmb::create($data);

        return redirect()->route('admin.konten-pmb.keunggulan.index')
            ->with('success', 'Keunggulan berhasil ditambahkan');
    }

    public function keunggulanEdit(KeunggulanPmb $keunggulan)
    {
        return view('admin.konten-pmb.keunggulan.edit', compact('keunggulan'));
    }

    public function keunggulanUpdate(Request $request, KeunggulanPmb $keunggulan)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['judul', 'deskripsi', 'icon', 'urutan']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('gambar')) {
            if ($keunggulan->gambar) {
                Storage::disk('public')->delete($keunggulan->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('pmb/keunggulan', 'public');
        }

        $keunggulan->update($data);

        return redirect()->route('admin.konten-pmb.keunggulan.index')
            ->with('success', 'Keunggulan berhasil diperbarui');
    }

    public function keunggulanDestroy(KeunggulanPmb $keunggulan)
    {
        if ($keunggulan->gambar) {
            Storage::disk('public')->delete($keunggulan->gambar);
        }
        $keunggulan->delete();

        return redirect()->route('admin.konten-pmb.keunggulan.index')
            ->with('success', 'Keunggulan berhasil dihapus');
    }

    // ==================
    // FASILITAS
    // ==================
    public function fasilitasIndex()
    {
        return view('admin.konten-pmb.fasilitas.index', [
            'fasilitass' => FasilitasPmb::orderBy('urutan')->get()
        ]);
    }

    public function fasilitasCreate()
    {
        return view('admin.konten-pmb.fasilitas.create');
    }

    public function fasilitasStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['nama', 'deskripsi', 'icon']);
        $data['urutan'] = FasilitasPmb::max('urutan') + 1;
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('pmb/fasilitas', 'public');
        }

        FasilitasPmb::create($data);

        return redirect()->route('admin.konten-pmb.fasilitas.index')
            ->with('success', 'Fasilitas berhasil ditambahkan');
    }

    public function fasilitasEdit(FasilitasPmb $fasilitas)
    {
        return view('admin.konten-pmb.fasilitas.edit', compact('fasilitas'));
    }

    public function fasilitasUpdate(Request $request, FasilitasPmb $fasilitas)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['nama', 'deskripsi', 'icon', 'urutan']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('gambar')) {
            if ($fasilitas->gambar) {
                Storage::disk('public')->delete($fasilitas->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('pmb/fasilitas', 'public');
        }

        $fasilitas->update($data);

        return redirect()->route('admin.konten-pmb.fasilitas.index')
            ->with('success', 'Fasilitas berhasil diperbarui');
    }

    public function fasilitasDestroy(FasilitasPmb $fasilitas)
    {
        if ($fasilitas->gambar) {
            Storage::disk('public')->delete($fasilitas->gambar);
        }
        $fasilitas->delete();

        return redirect()->route('admin.konten-pmb.fasilitas.index')
            ->with('success', 'Fasilitas berhasil dihapus');
    }

    // ==================
    // KONTAK
    // ==================
    public function kontakIndex()
    {
        return view('admin.konten-pmb.kontak.index', [
            'kontaks' => KontakPmb::orderBy('type')->orderBy('urutan')->get()
        ]);
    }

    public function kontakCreate()
    {
        return view('admin.konten-pmb.kontak.create');
    }

    public function kontakStore(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        KontakPmb::create([
            'type' => $request->type,
            'label' => $request->label,
            'value' => $request->value,
            'icon' => $request->icon,
            'link' => $request->link,
            'urutan' => KontakPmb::max('urutan') + 1,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.konten-pmb.kontak.index')
            ->with('success', 'Kontak berhasil ditambahkan');
    }

    public function kontakEdit(KontakPmb $kontak)
    {
        return view('admin.konten-pmb.kontak.edit', compact('kontak'));
    }

    public function kontakUpdate(Request $request, KontakPmb $kontak)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $kontak->update([
            'type' => $request->type,
            'label' => $request->label,
            'value' => $request->value,
            'icon' => $request->icon,
            'link' => $request->link,
            'urutan' => $request->urutan ?? $kontak->urutan,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.konten-pmb.kontak.index')
            ->with('success', 'Kontak berhasil diperbarui');
    }

    public function kontakDestroy(KontakPmb $kontak)
    {
        $kontak->delete();
        return redirect()->route('admin.konten-pmb.kontak.index')
            ->with('success', 'Kontak berhasil dihapus');
    }

    // ==================
    // PENGATURAN UMUM
    // ==================
    public function pengaturan()
    {
        $konten = KontenPmb::all()->pluck('value', 'key')->toArray();
        return view('admin.konten-pmb.pengaturan', compact('konten'));
    }

    public function pengaturanUpdate(Request $request)
    {
        $keys = [
            'nama_universitas', 'tagline', 'deskripsi_singkat', 'tahun_berdiri',
            'alamat_lengkap', 'google_maps_embed', 'logo', 'favicon',
            'hero_title', 'hero_subtitle', 'hero_background',
            'meta_title', 'meta_description', 'meta_keywords',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                $value = $request->$key;
                
                // Handle file upload
                if ($request->hasFile($key)) {
                    $value = $request->file($key)->store('pmb/settings', 'public');
                }

                KontenPmb::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'label' => ucwords(str_replace('_', ' ', $key)),
                        'group' => $this->getGroupFromKey($key),
                        'is_active' => true,
                    ]
                );
            }
        }

        return back()->with('success', 'Pengaturan berhasil disimpan');
    }

    private function getGroupFromKey($key)
    {
        if (str_starts_with($key, 'hero_')) return 'hero';
        if (str_starts_with($key, 'meta_')) return 'seo';
        if (in_array($key, ['logo', 'favicon'])) return 'branding';
        return 'general';
    }
}
