
Proje Görevi: ASA AI Sales Agent WordPress Eklentisi Geliştirme Talimatları
Genel Bakış

Geliştirilecek Ürün: ASA (AI Sales Agent), bir WordPress eklentisi olarak geliştirilmelidir.

Temel Fonksiyon: Bu eklenti, web sitelerine Google Gemini API tabanlı, yapay zeka destekli bir satış temsilcisi sohbet robotu entegre etmelidir.

Ana Yetenekler: Eklenti, ziyaretçilerle etkileşim kurmalı, sorularını yanıtlamalı, ürünleri tanıtmalı ve satış sürecine aktif olarak yardımcı olmalıdır. Tüm yapılandırma ve özelleştirme seçenekleri, kullanıcı dostu bir yönetici paneli üzerinden sunulmalıdır.

Geliştirilecek Eklentinin Temel İşlevleri (Gereksinimler)

ASA eklentisinin temel amacı, bir web sitesinin satış ve müşteri etkileşim süreçlerini otomatikleştirmek ve geliştirmektir. Bu amaçla aşağıdaki işlevler geliştirilmelidir:

Proaktif Müşteri Etkileşimi: Ziyaretçiler siteye giriş yaptığında, sohbet robotu otomatik olarak proaktif bir karşılama mesajı göndermelidir. Bu mesaj, ziyaretçinin o an görüntülediği sayfanın içeriğine göre kişiselleştirilebilir olmalıdır.

Bilgi Sağlama ve Soru Yanıtlama: Sohbet robotu, site içeriği hakkında bilgi sağlama, ürün veya hizmetlerle ilgili soruları yanıtlama ve sıkça sorulan sorulara anında cevap verme yeteneğine sahip olmalıdır.

Satış Odaklı Rehberlik: Sohbet robotu, ziyaretçileri ürün sayfalarına yönlendirebilmeli, ürün özelliklerini vurgulayabilmeli ve satın alma sürecine doğru nazikçe rehberlik edebilmelidir.

Kişiselleştirilmiş Deneyim: Gemini API'nin yetenekleri kullanılarak, sohbet robotu kullanıcıların önceki etkileşimlerini ve mevcut sayfa bağlamını dikkate alarak daha kişiselleştirilmiş ve alakalı yanıtlar sunmalıdır.

24/7 Destek: Eklenti, müşterilere günün her saati anında destek sağlamalı, böylece insan müdahalesi gerektiren durumları azaltmalı ve müşteri memnuniyetini artırmalıdır.

Uygulanacak Özellikler

Gemini API Entegrasyonu: Eklenti, kullanıcının Google Gemini API anahtarını girmesine ve bu anahtar ile sohbet robotunu etkinleştirmesine olanak tanımalıdır.

Özelleştirilebilir Kişilik (Sistem İstemcisi): Sohbet robotunun rolünü, kişiliğini ve satış odaklı yanıtlarını şekillendirmek için kullanıcının bir sistem istemcisi (system prompt) tanımlayabileceği bir alan sağlanmalıdır.

Görünüm Özelleştirmesi: Eklenti aşağıdaki görünüm özelleştirme seçeneklerini sunmalıdır:

Başlık ve Alt Başlık: Sohbet penceresinin başlığını ve alt başlığını ayarlamak için metin giriş alanları bulunmalıdır.

Birincil Renk: Sohbet robotu arayüzünün ana rengini seçmek için bir renk seçici (color picker) sağlanmalıdır.

Bot Avatarı: Önceden hazırlanmış icon avatarlardan seçim yapma veya özel bir resim (PNG, JPG, GIF) yükleme imkanı sunulmalıdır.

Konum: Sohbet robotu başlatıcısının sayfadaki konumunu (sol alt veya sağ alt) seçmek için bir seçenek olmalıdır.

Geliştirici Kredisi: "Geliştirici" kredisinin gösterilip gösterilmeyeceğini kontrol etmek için bir anahtar (toggle) bulunmalıdır.

Proaktif Mesajlaşma: Sohbet robotu, kullanıcıya bulunduğu sayfanın içeriğine göre proaktif bir karşılama mesajı oluşturup sunmalıdır.

Sayfa İçeriği Bağlamı: Sohbet robotu, yanıtlarını oluştururken kullanıcının o an görüntülediği sayfanın içeriğini bir bağlam olarak anlayıp kullanabilmelidir.

Kullanıcı Dostu Yönetici Paneli: Tüm ayarlar, sezgisel bir yönetici paneli arayüzü üzerinden kolayca yapılandırılabilir olmalıdır.

Duyarlı Tasarım: Sohbet robotu arayüzü hem masaüstü hem de mobil cihazlarda sorunsuz bir şekilde görünmeli ve çalışmalıdır.

API Başlatma Mantığı: Kullanıcının sağladığı API anahtarı ile Gemini API istemcisini başlatacak bir mekanizma kurulmalıdır. Anahtar değiştiğinde veya geçersiz olduğunda yeniden başlatma ve hata yönetimi yapılmalıdır.

Proaktif Mesaj Oluşturma Fonksiyonu (generateProactiveMessage):

Bu fonksiyon, gemini-2.5-flash modelini kullanmalıdır.

Sistem istemcisini ve kullanıcının o anki sayfa içeriğinin ilk 2000 karakterini girdi olarak almalıdır.

Yeni bir sohbet oturumu başlatarak, kullanıcının dikkatini çekecek, sayfa içeriğiyle alakalı kısa ve etkileşimli bir soru veya ifade oluşturmalıdır.

API anahtarı sorunları gibi hata durumlarında, varsayılan bir karşılama mesajı sağlamalıdır.

Yönetici Paneli Detayları (Geliştirme Kılavuzu)

Eklenti için, tüm ayarların yapılandırılabileceği, kullanıcı dostu ve sekmeli bir yapıya sahip bir yönetici paneli oluşturulmalıdır:

Genel Sekmesi: Bu sekme oluşturulmalı ve aşağıdaki alanları içermelidir:

Gemini API Yapılandırması: Kullanıcının Gemini API anahtarını girebileceği bir metin alanı sağlanmalıdır. Bu alanın yanına, kullanıcıları Google AI Studio'dan ücretsiz anahtar alabilecekleri konusunda bilgilendiren bir bağlantı eklenmelidir.

Geliştirici Desteği: Geliştiriciye destek olmak için "Buy Me a Coffee" ve "Contact Us" bağlantıları bu sekmede yer almalıdır.

Görünüm Sekmesi: Bu sekme aşağıdaki alt bölümleri içermelidir:

Markalama:

Başlık: Sohbet penceresinin ana başlığını girmek için bir metin alanı.

Alt Başlık: Başlığın altında görünecek alt başlığı girmek için bir metin alanı.

Birincil Renk: Sohbet arayüzünün ana rengini belirlemek için bir renk paleti ve özel renk seçici.

Asistan Avatarı:

Hazır Avatarlar: Kullanıcının seçebileceği, önceden tanımlanmış çeşitli bot avatarları (radio button veya tıklanabilir resimler).

Özel Avatar: Kullanıcının kendi resmini (PNG, JPEG, GIF) yüklemesine olanak tanıyan bir dosya yükleme alanı. Yüklenen resmin bir önizlemesi gösterilmeli ve resmi kaldırmak için bir seçenek sunulmalıdır.

Diğer Görünüm Ayarları:

Sayfadaki Konum: Sohbet başlatıcısının konumunu (sol alt / sağ alt) seçmek için bir radio button grubu.

Geliştirici Kredisi: Sohbet penceresinin altında "Developed by: Adem İşler" kredisinin gösterilip gösterilmeyeceğini kontrol eden bir açma/kapama düğmesi (toggle switch).

Davranış Sekmesi: Bu sekme aşağıdaki alanı içermelidir:

Kişilik (Sistem İstemcisi): Sohbet robotunun temel davranışını, rolünü ve yanıt stilini tanımlamak için geniş bir metin alanı (textarea). Örnek bir istem metni placeholder olarak eklenebilir.

Yönetici paneline, yapılan değişikliklerin anında nasıl görüneceğini gösteren bir "Canlı Önizleme" bölümü eklenmelidir. Ayarlar kaydedilirken bir "Kaydediliyor..." ve ardından "Kaydedildi!" durumu göstergesi ile kullanıcı bilgilendirilmelidir.

Chatbot Ön Yüz Görünümü ve Etkileşimi (UI/UX Gereksinimleri)

Sitenin ön yüzünde, sohbet robotu aşağıdaki iki ana duruma sahip olmalıdır:

Kapalı Durum (Başlatıcı Düğme):

Yönetici panelinde belirlenen köşede (sol alt veya sağ alt) küçük, yuvarlak bir düğme olarak konumlandırılmalıdır.

Bu düğme, bir sohbet balonu simgesi ve sohbet robotunun proaktif mesajının kısa bir versiyonunu göstermelidir.

Yükleme durumunda, proaktif mesaj yerine animasyonlu üç nokta (...) gösterilmelidir.

Düğmenin rengi, yönetici panelinde belirlenen birincil renkte olmalıdır.

Kullanıcının dikkatini çekmek için hover ve active durumlarında hafif animasyonlara sahip olmalıdır.

Açık Durum (Sohbet Penceresi):

Başlatıcı düğmeye tıklandığında, sohbet penceresi açılmalıdır (mobil için tam ekran, masaüstü için sabit boyutlu bir pencere).

Başlık Bölümü:

Botun avatarı, başlığı ve alt başlığı bu bölümde gösterilmelidir.

Birincil renkte küçük bir online durum göstergesi (yeşil nokta) bulunmalıdır.

Pencereyi kapatmak için bir "X" simgesi olmalıdır.

Mesaj Alanı:

Konuşmaların gösterildiği, kaydırılabilir bir alan tasarlanmalıdır.

Kullanıcı mesajları sağda, bot mesajları solda hizalanmalıdır.

Mesaj baloncukları, yuvarlak köşeli ve birincil renk ile gri tonlarında olmalıdır.

Her bot mesajının yanında botun avatarı gösterilmelidir.

Bot yanıt yazarken, yazıyor olduğunu belirten animasyonlu üç nokta (...) gösterilmelidir.

Giriş Alanı:

Kullanıcının mesaj yazabileceği bir metin giriş kutusu bulunmalıdır.

Mesajı göndermek için bir "Gönder" düğmesi (birincil renkte) olmalıdır.

Eğer API anahtarı yapılandırılmamışsa, bu giriş alanı devre dışı bırakılmalı ve kullanıcıyı bilgilendiren bir uyarı mesajı gösterilmelidir.

Geliştirici Kredisi: Yönetici panelinde etkinleştirilmişse, sohbet penceresinin altında "Developed by: Adem İşler" yazısı görünmelidir.

Geliştirici Bilgileri (Eklentiye Eklenecek Bilgiler)

Eklentinin geliştiricisi olarak Adem İşler belirtilmelidir. Bu bilgi, kullanıcıların destek alabileceği ve iletişim kurabileceği aşağıdaki bağlantılarla birlikte yönetici panelinde veya eklenti açıklamasında yer almalıdır:


*   **Web Sitesi:** [ademisler.com](https://ademisler.com)
*   **Destek Ol:** [Buy Me a Coffee](https://buymeacoffee.com/ademisler)
*   **İletişim:** [İletişim Formu](https://ademisler.com/iletisim)

## Installation

Upload the `asa-plugin` directory to your `wp-content/plugins` folder and activate the "ASA AI Sales Agent" plugin from the WordPress admin panel.
