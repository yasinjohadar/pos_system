<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تعطيل التحقق من المفاتيح الأجنبية مؤقتاً
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // حذف البيانات القديمة
        Product::truncate();
        Category::truncate();

        // إعادة تفعيل التحقق من المفاتيح الأجنبية
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // إنشاء 10 تصنيفات
        $categories = [
            [
                'name' => 'الأجهزة الإلكترونية',
                'description' => 'جميع أنواع الأجهزة الإلكترونية والرقمية',
                'status' => 'active',
                'order' => 1,
            ],
            [
                'name' => 'الملابس والأزياء',
                'description' => 'أحدث صيحات الموضة والملابس',
                'status' => 'active',
                'order' => 2,
            ],
            [
                'name' => 'الأثاث والديكور',
                'description' => 'قطع الأثاث المنزلي والديكور',
                'status' => 'active',
                'order' => 3,
            ],
            [
                'name' => 'المنتجات الرياضية',
                'description' => 'المعدات والملابس الرياضية',
                'status' => 'active',
                'order' => 4,
            ],
            [
                'name' => 'الكتب والمجلات',
                'description' => 'مجموعة متنوعة من الكتب والمجلات',
                'status' => 'active',
                'order' => 5,
            ],
            [
                'name' => 'المنتجات الصحية',
                'description' => 'منتجات العناية الصحية والطبية',
                'status' => 'active',
                'order' => 6,
            ],
            [
                'name' => 'المنتجات الغذائية',
                'description' => 'الأطعمة والمشروبات المختلفة',
                'status' => 'active',
                'order' => 7,
            ],
            [
                'name' => 'الألعاب والهدايا',
                'description' => 'ألعاب الأطفال والهدايا',
                'status' => 'active',
                'order' => 8,
            ],
            [
                'name' => 'الإكسسوارات',
                'description' => 'إكسسوارات متنوعة للرجال والنساء',
                'status' => 'active',
                'order' => 9,
            ],
            [
                'name' => 'المنتجات المنزلية',
                'description' => 'الأدوات والمستلزمات المنزلية',
                'status' => 'active',
                'order' => 10,
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $index => $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'status' => $categoryData['status'],
                'order' => $categoryData['order'],
                'meta_title' => $categoryData['name'],
                'meta_description' => $categoryData['description'],
            ]);
            $createdCategories[] = $category;
            $this->command->info("تم إنشاء التصنيف: {$category->name}");
        }

        // بيانات المنتجات لكل تصنيف
        $productsData = [
            // الأجهزة الإلكترونية
            [
                'category_id' => $createdCategories[0]->id,
                'products' => [
                    ['name' => 'هاتف ذكي برو', 'price' => 2999.00, 'stock' => 50],
                    ['name' => 'لابتوب عالي الأداء', 'price' => 5999.00, 'stock' => 30],
                    ['name' => 'سماعات لاسلكية', 'price' => 499.00, 'stock' => 100],
                    ['name' => 'تابلت أندرويد', 'price' => 1599.00, 'stock' => 45],
                    ['name' => 'ساعة ذكية', 'price' => 899.00, 'stock' => 60],
                    ['name' => 'كاميرا رقمية', 'price' => 2499.00, 'stock' => 25],
                    ['name' => 'شاحن سريع', 'price' => 199.00, 'stock' => 150],
                    ['name' => 'لوحة مفاتيح ميكانيكية', 'price' => 399.00, 'stock' => 80],
                    ['name' => 'ماوس جيمنج', 'price' => 299.00, 'stock' => 90],
                    ['name' => 'شاشة 27 بوصة', 'price' => 1299.00, 'stock' => 35],
                    ['name' => 'سماعات رأس جيمنج', 'price' => 599.00, 'stock' => 70],
                    ['name' => 'مكبر صوت بلوتوث', 'price' => 349.00, 'stock' => 55],
                    ['name' => 'قلم رقمي', 'price' => 249.00, 'stock' => 85],
                    ['name' => 'قرص صلب خارجي', 'price' => 449.00, 'stock' => 65],
                    ['name' => 'طابعة ليزر', 'price' => 899.00, 'stock' => 20],
                    ['name' => 'ماسح ضوئي', 'price' => 699.00, 'stock' => 15],
                    ['name' => 'بورسبل بانك', 'price' => 149.00, 'stock' => 120],
                    ['name' => 'كابل USB-C', 'price' => 79.00, 'stock' => 200],
                    ['name' => 'حامل لابتوب', 'price' => 129.00, 'stock' => 75],
                    ['name' => 'منظف شاشات', 'price' => 49.00, 'stock' => 180],
                ],
            ],
            // الملابس والأزياء
            [
                'category_id' => $createdCategories[1]->id,
                'products' => [
                    ['name' => 'قميص رجالي أنيق', 'price' => 199.00, 'stock' => 100],
                    ['name' => 'بنطال جينز', 'price' => 249.00, 'stock' => 80],
                    ['name' => 'فستان نسائي', 'price' => 399.00, 'stock' => 60],
                    ['name' => 'جاكيت شتوي', 'price' => 599.00, 'stock' => 40],
                    ['name' => 'حذاء رياضي', 'price' => 449.00, 'stock' => 70],
                    ['name' => 'حقيبة يد', 'price' => 299.00, 'stock' => 50],
                    ['name' => 'ساعة يد', 'price' => 799.00, 'stock' => 35],
                    ['name' => 'نظارة شمسية', 'price' => 199.00, 'stock' => 90],
                    ['name' => 'حزام جلد', 'price' => 149.00, 'stock' => 110],
                    ['name' => 'قبعة صيفية', 'price' => 99.00, 'stock' => 130],
                    ['name' => 'قميص قطني', 'price' => 149.00, 'stock' => 120],
                    ['name' => 'بنطال رياضي', 'price' => 179.00, 'stock' => 95],
                    ['name' => 'بلوزة نسائية', 'price' => 229.00, 'stock' => 85],
                    ['name' => 'تنورة قصيرة', 'price' => 189.00, 'stock' => 75],
                    ['name' => 'حذاء كلاسيكي', 'price' => 549.00, 'stock' => 45],
                    ['name' => 'وشاح صوفي', 'price' => 129.00, 'stock' => 100],
                    ['name' => 'قفازات شتوية', 'price' => 99.00, 'stock' => 140],
                    ['name' => 'ملابس داخلية', 'price' => 79.00, 'stock' => 200],
                    ['name' => 'جوارب قطنية', 'price' => 49.00, 'stock' => 250],
                    ['name' => 'طقم نوم', 'price' => 199.00, 'stock' => 65],
                ],
            ],
            // الأثاث والديكور
            [
                'category_id' => $createdCategories[2]->id,
                'products' => [
                    ['name' => 'طاولة طعام', 'price' => 1299.00, 'stock' => 15],
                    ['name' => 'كرسي مريح', 'price' => 599.00, 'stock' => 40],
                    ['name' => 'سرير مزدوج', 'price' => 2499.00, 'stock' => 10],
                    ['name' => 'خزانة ملابس', 'price' => 1899.00, 'stock' => 20],
                    ['name' => 'طاولة مكتب', 'price' => 899.00, 'stock' => 25],
                    ['name' => 'أريكة ثلاثية', 'price' => 3299.00, 'stock' => 8],
                    ['name' => 'رف كتب', 'price' => 699.00, 'stock' => 30],
                    ['name' => 'مصباح طاولة', 'price' => 199.00, 'stock' => 60],
                    ['name' => 'لوحة فنية', 'price' => 299.00, 'stock' => 50],
                    ['name' => 'سجادة صوف', 'price' => 799.00, 'stock' => 25],
                    ['name' => 'طاولة جانبية', 'price' => 349.00, 'stock' => 45],
                    ['name' => 'كرسي مكتب', 'price' => 799.00, 'stock' => 35],
                    ['name' => 'خزانة تخزين', 'price' => 599.00, 'stock' => 40],
                    ['name' => 'مرآة ديكور', 'price' => 249.00, 'stock' => 70],
                    ['name' => 'مفرش سرير', 'price' => 199.00, 'stock' => 80],
                    ['name' => 'وسائد مريحة', 'price' => 99.00, 'stock' => 100],
                    ['name' => 'ستارة نوافذ', 'price' => 149.00, 'stock' => 60],
                    ['name' => 'معلق ملابس', 'price' => 79.00, 'stock' => 90],
                    ['name' => 'حامل نباتات', 'price' => 129.00, 'stock' => 55],
                    ['name' => 'سلة مهملات', 'price' => 89.00, 'stock' => 70],
                ],
            ],
            // المنتجات الرياضية
            [
                'category_id' => $createdCategories[3]->id,
                'products' => [
                    ['name' => 'دراجة هوائية', 'price' => 1499.00, 'stock' => 20],
                    ['name' => 'أثاث جيمنج', 'price' => 2999.00, 'stock' => 10],
                    ['name' => 'كرة قدم', 'price' => 149.00, 'stock' => 100],
                    ['name' => 'ملابس رياضية', 'price' => 199.00, 'stock' => 80],
                    ['name' => 'حذاء جري', 'price' => 449.00, 'stock' => 60],
                    ['name' => 'أوزان تدريب', 'price' => 599.00, 'stock' => 40],
                    ['name' => 'حبل القفز', 'price' => 49.00, 'stock' => 150],
                    ['name' => 'كرة سلة', 'price' => 129.00, 'stock' => 90],
                    ['name' => 'خوذة ركوب', 'price' => 199.00, 'stock' => 70],
                    ['name' => 'قفازات ملاكمة', 'price' => 149.00, 'stock' => 85],
                    ['name' => 'شريط مقاومة', 'price' => 79.00, 'stock' => 120],
                    ['name' => 'حقيبة رياضية', 'price' => 199.00, 'stock' => 65],
                    ['name' => 'زجاجة ماء', 'price' => 59.00, 'stock' => 200],
                    ['name' => 'بساط يوغا', 'price' => 129.00, 'stock' => 75],
                    ['name' => 'سماعة رياضية', 'price' => 399.00, 'stock' => 55],
                    ['name' => 'عداد خطوات', 'price' => 249.00, 'stock' => 80],
                    ['name' => 'كرة تنس', 'price' => 99.00, 'stock' => 150],
                    ['name' => 'طقم سباحة', 'price' => 299.00, 'stock' => 45],
                    ['name' => 'نظارات سباحة', 'price' => 149.00, 'stock' => 70],
                    ['name' => 'ملابس سباحة', 'price' => 249.00, 'stock' => 55],
                ],
            ],
            // الكتب والمجلات
            [
                'category_id' => $createdCategories[4]->id,
                'products' => [
                    ['name' => 'رواية عالمية', 'price' => 49.00, 'stock' => 100],
                    ['name' => 'كتاب تعليمي', 'price' => 79.00, 'stock' => 80],
                    ['name' => 'مجلة شهرية', 'price' => 29.00, 'stock' => 150],
                    ['name' => 'كتاب أطفال', 'price' => 39.00, 'stock' => 120],
                    ['name' => 'قاموس عربي', 'price' => 99.00, 'stock' => 60],
                    ['name' => 'كتاب تاريخ', 'price' => 69.00, 'stock' => 70],
                    ['name' => 'رواية خيال', 'price' => 59.00, 'stock' => 90],
                    ['name' => 'كتاب طبخ', 'price' => 89.00, 'stock' => 65],
                    ['name' => 'مجلة تقنية', 'price' => 35.00, 'stock' => 110],
                    ['name' => 'كتاب فلسفة', 'price' => 79.00, 'stock' => 50],
                    ['name' => 'رواية كلاسيكية', 'price' => 45.00, 'stock' => 85],
                    ['name' => 'كتاب علوم', 'price' => 99.00, 'stock' => 55],
                    ['name' => 'مجلة أزياء', 'price' => 39.00, 'stock' => 95],
                    ['name' => 'كتاب شعر', 'price' => 49.00, 'stock' => 75],
                    ['name' => 'رواية بوليسية', 'price' => 55.00, 'stock' => 80],
                    ['name' => 'كتاب رياضيات', 'price' => 89.00, 'stock' => 45],
                    ['name' => 'مجلة سفر', 'price' => 42.00, 'stock' => 70],
                    ['name' => 'كتاب فن', 'price' => 149.00, 'stock' => 40],
                    ['name' => 'رواية رومانسية', 'price' => 49.00, 'stock' => 90],
                    ['name' => 'كتاب ديني', 'price' => 59.00, 'stock' => 65],
                ],
            ],
            // المنتجات الصحية
            [
                'category_id' => $createdCategories[5]->id,
                'products' => [
                    ['name' => 'فيتامينات متعددة', 'price' => 99.00, 'stock' => 100],
                    ['name' => 'مقياس حرارة', 'price' => 79.00, 'stock' => 80],
                    ['name' => 'ضغط دم', 'price' => 199.00, 'stock' => 50],
                    ['name' => 'قناع وجه', 'price' => 149.00, 'stock' => 120],
                    ['name' => 'كريم مرطب', 'price' => 129.00, 'stock' => 90],
                    ['name' => 'واقي شمسي', 'price' => 89.00, 'stock' => 110],
                    ['name' => 'شامبو طبيعي', 'price' => 79.00, 'stock' => 130],
                    ['name' => 'معجون أسنان', 'price' => 29.00, 'stock' => 200],
                    ['name' => 'فرشاة أسنان', 'price' => 19.00, 'stock' => 250],
                    ['name' => 'غسول وجه', 'price' => 99.00, 'stock' => 85],
                    ['name' => 'كريم ليلي', 'price' => 149.00, 'stock' => 70],
                    ['name' => 'تونر وجه', 'price' => 119.00, 'stock' => 75],
                    ['name' => 'مكملات غذائية', 'price' => 179.00, 'stock' => 60],
                    ['name' => 'أعشاب طبية', 'price' => 59.00, 'stock' => 140],
                    ['name' => 'زيوت عطرية', 'price' => 89.00, 'stock' => 95],
                    ['name' => 'صابون طبيعي', 'price' => 39.00, 'stock' => 180],
                    ['name' => 'مناديل معقمة', 'price' => 29.00, 'stock' => 220],
                    ['name' => 'قفازات طبية', 'price' => 49.00, 'stock' => 200],
                    ['name' => 'كمامة طبية', 'price' => 59.00, 'stock' => 150],
                    ['name' => 'علاج سعال', 'price' => 69.00, 'stock' => 90],
                ],
            ],
            // المنتجات الغذائية
            [
                'category_id' => $createdCategories[6]->id,
                'products' => [
                    ['name' => 'قهوة فاخرة', 'price' => 149.00, 'stock' => 80],
                    ['name' => 'شاي أخضر', 'price' => 79.00, 'stock' => 100],
                    ['name' => 'عسل طبيعي', 'price' => 199.00, 'stock' => 60],
                    ['name' => 'زيت زيتون', 'price' => 89.00, 'stock' => 90],
                    ['name' => 'مكسرات مشكلة', 'price' => 129.00, 'stock' => 70],
                    ['name' => 'حبوب إفطار', 'price' => 49.00, 'stock' => 120],
                    ['name' => 'عصير طبيعي', 'price' => 19.00, 'stock' => 200],
                    ['name' => 'مياه معدنية', 'price' => 9.00, 'stock' => 300],
                    ['name' => 'حليب طازج', 'price' => 12.00, 'stock' => 250],
                    ['name' => 'جبن أبيض', 'price' => 29.00, 'stock' => 150],
                    ['name' => 'زبدة فول سوداني', 'price' => 39.00, 'stock' => 110],
                    ['name' => 'مربى فواكه', 'price' => 35.00, 'stock' => 130],
                    ['name' => 'شوكولاتة', 'price' => 25.00, 'stock' => 200],
                    ['name' => 'بسكويت', 'price' => 15.00, 'stock' => 250],
                    ['name' => 'سكر', 'price' => 8.00, 'stock' => 300],
                    ['name' => 'ملح', 'price' => 5.00, 'stock' => 350],
                    ['name' => 'بهارات', 'price' => 29.00, 'stock' => 140],
                    ['name' => 'أرز', 'price' => 19.00, 'stock' => 200],
                    ['name' => 'معكرونة', 'price' => 12.00, 'stock' => 220],
                    ['name' => 'زيت نباتي', 'price' => 25.00, 'stock' => 180],
                ],
            ],
            // الألعاب والهدايا
            [
                'category_id' => $createdCategories[7]->id,
                'products' => [
                    ['name' => 'لعبة مكعبات', 'price' => 99.00, 'stock' => 80],
                    ['name' => 'دمية متحركة', 'price' => 149.00, 'stock' => 60],
                    ['name' => 'سيارة ألعاب', 'price' => 79.00, 'stock' => 100],
                    ['name' => 'لعبة لوحية', 'price' => 199.00, 'stock' => 50],
                    ['name' => 'ألوان مائية', 'price' => 59.00, 'stock' => 120],
                    ['name' => 'دفتر تلوين', 'price' => 29.00, 'stock' => 150],
                    ['name' => 'قلم رصاص', 'price' => 9.00, 'stock' => 300],
                    ['name' => 'ممحاة', 'price' => 5.00, 'stock' => 350],
                    ['name' => 'باقة ورد', 'price' => 199.00, 'stock' => 40],
                    ['name' => 'صندوق هدايا', 'price' => 79.00, 'stock' => 70],
                    ['name' => 'بطاقة تهنئة', 'price' => 19.00, 'stock' => 200],
                    ['name' => 'شريط هدايا', 'price' => 15.00, 'stock' => 180],
                    ['name' => 'لعبة بلياردو', 'price' => 499.00, 'stock' => 20],
                    ['name' => 'لعبة شطرنج', 'price' => 149.00, 'stock' => 60],
                    ['name' => 'لعبة دومينو', 'price' => 79.00, 'stock' => 90],
                    ['name' => 'ألوان خشبية', 'price' => 49.00, 'stock' => 130],
                    ['name' => 'مقص أطفال', 'price' => 19.00, 'stock' => 200],
                    ['name' => 'غراء آمن', 'price' => 15.00, 'stock' => 220],
                    ['name' => 'لعبة بناء', 'price' => 299.00, 'stock' => 45],
                    ['name' => 'طقم ألعاب', 'price' => 399.00, 'stock' => 35],
                ],
            ],
            // الإكسسوارات
            [
                'category_id' => $createdCategories[8]->id,
                'products' => [
                    ['name' => 'خاتم ذهبي', 'price' => 599.00, 'stock' => 30],
                    ['name' => 'قلادة فضية', 'price' => 399.00, 'stock' => 40],
                    ['name' => 'أساور متنوعة', 'price' => 149.00, 'stock' => 80],
                    ['name' => 'أقراط أذن', 'price' => 199.00, 'stock' => 60],
                    ['name' => 'سلسلة ذهبية', 'price' => 799.00, 'stock' => 25],
                    ['name' => 'مشبك ربط', 'price' => 99.00, 'stock' => 100],
                    ['name' => 'دبوس زينة', 'price' => 79.00, 'stock' => 110],
                    ['name' => 'طقم مجوهرات', 'price' => 1299.00, 'stock' => 15],
                    ['name' => 'حقيبة ظهر', 'price' => 299.00, 'stock' => 50],
                    ['name' => 'محفظة جلدية', 'price' => 199.00, 'stock' => 70],
                    ['name' => 'حقيبة سفر', 'price' => 499.00, 'stock' => 35],
                    ['name' => 'حقيبة كتف', 'price' => 249.00, 'stock' => 55],
                    ['name' => 'منديل حرير', 'price' => 129.00, 'stock' => 90],
                    ['name' => 'عقد رقبة', 'price' => 349.00, 'stock' => 45],
                    ['name' => 'طقم أزرار', 'price' => 99.00, 'stock' => 80],
                    ['name' => 'ربطة عنق', 'price' => 149.00, 'stock' => 70],
                    ['name' => 'دبوس أكمام', 'price' => 79.00, 'stock' => 95],
                    ['name' => 'سوار معصم', 'price' => 199.00, 'stock' => 65],
                    ['name' => 'خاتم فضي', 'price' => 299.00, 'stock' => 50],
                    ['name' => 'طقم مجوهرات فضية', 'price' => 899.00, 'stock' => 20],
                ],
            ],
            // المنتجات المنزلية
            [
                'category_id' => $createdCategories[9]->id,
                'products' => [
                    ['name' => 'مكنسة كهربائية', 'price' => 799.00, 'stock' => 30],
                    ['name' => 'مروحة سقف', 'price' => 499.00, 'stock' => 40],
                    ['name' => 'مكيف هواء', 'price' => 2499.00, 'stock' => 15],
                    ['name' => 'غسالة ملابس', 'price' => 1999.00, 'stock' => 20],
                    ['name' => 'ثلاجة كبيرة', 'price' => 2999.00, 'stock' => 12],
                    ['name' => 'فرن كهربائي', 'price' => 1499.00, 'stock' => 25],
                    ['name' => 'غلاية ماء', 'price' => 199.00, 'stock' => 80],
                    ['name' => 'خلاط كهربائي', 'price' => 299.00, 'stock' => 60],
                    ['name' => 'محضر طعام', 'price' => 499.00, 'stock' => 45],
                    ['name' => 'مقلاة كهربائية', 'price' => 399.00, 'stock' => 50],
                    ['name' => 'ماكينة قهوة', 'price' => 899.00, 'stock' => 35],
                    ['name' => 'محمصة خبز', 'price' => 249.00, 'stock' => 70],
                    ['name' => 'ميزان مطبخ', 'price' => 149.00, 'stock' => 90],
                    ['name' => 'سكين مطبخ', 'price' => 99.00, 'stock' => 100],
                    ['name' => 'لوح تقطيع', 'price' => 79.00, 'stock' => 110],
                    ['name' => 'أواني طهي', 'price' => 599.00, 'stock' => 55],
                    ['name' => 'طقم صحون', 'price' => 399.00, 'stock' => 65],
                    ['name' => 'كؤوس شاي', 'price' => 149.00, 'stock' => 120],
                    ['name' => 'إبريق شاي', 'price' => 199.00, 'stock' => 80],
                    ['name' => 'سلة غسيل', 'price' => 99.00, 'stock' => 90],
                ],
            ],
        ];

        // إنشاء المنتجات
        $totalProducts = 0;
        foreach ($productsData as $categoryProducts) {
            $categoryId = $categoryProducts['category_id'];
            $categoryName = Category::find($categoryId)->name;

            foreach ($categoryProducts['products'] as $productData) {
                $product = Product::create([
                    'name' => $productData['name'],
                    'sku' => 'SKU-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                    'short_description' => $productData['name'] . ' عالي الجودة',
                    'description' => $productData['name'] . ' منتج ممتاز يناسب احتياجاتك. يتميز بالجودة العالية والأداء المتميز.',
                    'price' => $productData['price'],
                    'sale_price' => rand(0, 1) ? $productData['price'] * 0.9 : null,
                    'stock_quantity' => $productData['stock'],
                    'min_order_quantity' => 1,
                    'weight' => rand(1, 500) / 100,
                    'category_id' => $categoryId,
                    'status' => 'active',
                    'featured' => rand(0, 10) > 8,
                    'meta_title' => $productData['name'],
                    'meta_description' => $productData['name'] . ' عالي الجودة بأفضل الأسعار',
                ]);
                $totalProducts++;
            }

            $this->command->info("تم إنشاء " . count($categoryProducts['products']) . " منتج في تصنيف: {$categoryName}");
        }

        $this->command->info("تم إنشاء {$totalProducts} منتج بنجاح!");
        $this->command->info("تم إنشاء " . count($createdCategories) . " تصنيف بنجاح!");
    }
}
