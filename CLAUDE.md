Harika fikir. Aşağıdaki metni doğrudan paket reposuna CLAUDE.md olarak koyabilirsin.

# Laravel Data Exporter — Proje Beyni

## Amaç
Bu paket, farklı veri kaynaklarından (Eloquent Builder, Query Builder, Collection, Array, Raw SQL) export dosyaları üretir.
Hedefimiz:
- Tek API ile CSV, XLSX, JSON, SQL export
- Filament gibi UI katmanlarından bağımsız çalışmak
- Sync ve Async modda tutarlı davranmak
- Export geri bildirimini paket seviyesinde standartlaştırmak

## Kapsam
Paket sorumlulukları:
- Export source üretimi
- Exporter seçimi
- Dosya üretimi ve storage kaydı
- Async job orkestrasyonu
- Export durum bildirimi için altyapı

Paket dışı sorumluluklar:
- Resource bazlı UI kararları
- Uygulama özel yetkilendirme
- Uygulama özel mesaj metinleri

## Mimari Özeti
Katmanlar:
- Services: DataExporter servis giriş noktası
- Factories: ExporterFactory, ExportSourceFactory, ExportQueryBuilderFactory
- Sources: BuilderExportSource, ArrayExportSource, SqlExportSource
- Exporters: CsvExporter, XlsxExporter, JsonExporter, SqlExporter
- Jobs: HandleExportJob
- DTO: ExportedFileDTO
- Notifications: ExportReadyNotification

Akış:
1. DataExporter export çağrısı alır
2. Source ve Exporter factory ile oluşturulur
3. Async ise job dispatch edilir, sync ise doğrudan handle çalışır
4. Exporter dosyayı üretir ve ExportedFileDTO döner
5. Bildirim stratejisine göre kullanıcıya geri bildirim üretilir

## Kritik Teknik Kurallar
1. Exporter sınıf adı ve dosya adı birebir uyumlu olmalı
- Örnek: XlsxExporter sınıfı XlsxExporter.php içinde olmalı

2. PhpSpreadsheet API uyumu korunmalı
- Kaldırılmış metotlar kullanılmamalı
- Hücre yazımı setCellValue ile yapılmalı

3. Hücre değeri normalize edilmeli
- Array ve object değerler stringe çevrilmeden hücreye yazılmamalı
- JSON string dönüşümü kullanılmalı

4. Paket seviyesi davranış tercih edilmeli
- Aynı export geri bildirim kodu resource resource kopyalanmamalı
- Ortak davranış paket içine taşınmalı

## Mevcut Bilinen İyileştirme Hedefi
Şu an bazı resource akışlarında:
- Selected Rows ve Current Page sync export çalışıyor ama kullanıcı geri bildirimi zayıf olabiliyor
- All Pages async export için started bildirimi var, ready bildirimi geliştirilmeli

Hedef:
- Sync export sonrası paketten tutarlı başarı dönüşü
- Async export için started ve ready döngüsünü tamamlamak
- Resource katmanında tekrar eden notification kodunu azaltmak

## Önerilen Geliştirme Planı
1. DataExporter dönüş tipini iyileştir
- Sadece void yerine sonuç dönecek bir yapı tasarla
- Örnek yaklaşım:
  - Sync: ExportedFileDTO
  - Async: dispatch sonucu ya da OperationResult

2. HandleExportJob ve Notification akışını tamamla
- ExportReadyNotification içeriğini doldur
- Dosya adı, format, indirme yolu bilgisi taşınabilsin

3. Notification stratejisi ekle
- Paket config ile kontrol:
  - none
  - started_only
  - completed_only
  - started_and_completed

4. Resource tarafını sadeleştir
- Resource sadece export çağrısı yapsın
- Paket ortak feedback davranışını üstlensin

## Kod Standartları
- PHP 8.2+ uyumlu, strict ve okunabilir kod
- Erken return ve küçük metotlar
- Public API geriye dönük uyumluluğa dikkat
- İsimlendirme açık ve niyet belirten olmalı
- Exception mesajları aksiyon alınabilir olmalı

## Test Stratejisi
En az şu senaryolar testlenmeli:
1. CSV sync export başarılı
2. XLSX sync export başarılı
3. XLSX export içinde array alan normalize edilerek yazılır
4. Async export job kuyruğa düşer
5. Unsupported format doğru exception verir
6. Selected mode boş seçimde uygun uyarı davranışı

## Release ve Sürümleme
- SemVer kullan
- Patch: bug fix
- Minor: geriye uyumlu yeni özellik
- Major: kırıcı değişiklik

Release notlarında mutlaka belirt:
- Hangi exporter düzeltildi
- API davranış değişikliği
- Migration gerekip gerekmediği
- Uygulama tarafında yapılacak update adımı

## Yerel Geliştirme Notları
- Uygulama reposunda vendor hotfix geçici çözümdür
- Kalıcı çözüm paket reposuna commit ve tag ile yayınlanmalı
- Uygulama tarafında yeni paket sürümüne composer update ile geçilmeli

## Bu Repo İçin Çalışma Prensibi
- Önce root cause, sonra minimal fix
- Aynı bug tekrar etmesin diye test ekle
- UI davranışı gerekiyorsa önce paketten çözmeyi dene
- Resource bazlı kopya kod son çare olsun

---

İstersen bir sonraki adımda bunun üstüne ikinci bir dosya da hazırlayabilirim:
- CONTRIBUTING.md taslağı (branch, commit, release checklist)
- veya kısa bir ROADMAP.md (v1.0.2, v1.1.0 hedefleri)
