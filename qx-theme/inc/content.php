<?php
/**
 * Site copy, both languages.
 *
 * THIS FILE SHIPS DEMO COPY. Every string below is neutral placeholder text written for this
 * repository, so the theme installs and renders a complete page without carrying any real
 * brand's wording or claims. Replace it with your own.
 *
 * TO EDIT TEXT: change the strings below. Arabic is the first value, English the second,
 * chosen by the `$ar` flag. Blog posts and any other pages stay fully editable in wp-admin.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 *  CLAIMS NEED SOURCES
 *
 *  Anything marked QX_PLACEHOLDER is a figure, a client, a quote or a credential that a
 *  business would be publishing about itself. The demo values are deliberately unmistakable
 *  ("00+", "CLIENT ONE") so they cannot be read as real results. Before launch, replace each
 *  with a verified, sourced number and the client's written permission, or delete the
 *  section. Search this file for QX_PLACEHOLDER to find every one.
 *
 *    • $stats           the four hero metrics
 *    • $cases           the three case studies
 *    • $clients         the client wordmark strip
 *    • $testimonial     the quote, its attribution and the partner badges
 *    • aboutPage.stats  founding year, team size, client count, sectors
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * @package QX
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The full content tree for the current language.
 *
 * @return array<string, mixed>
 */
function qx_content(): array {
	$ar = qx_is_ar();

	return array(

		/* ---- NAV ---------------------------------------------------------- */
		'nav' => array(
			'cta'  => $ar ? 'احجز استشارة' : 'Book a consultation',
			'menu' => $ar
				? array( 'الرئيسية' => '/', 'من نحن' => '/من-نحن/', 'خدماتنا' => '/خدماتنا/', 'تواصل معنا' => '/تواصل-معنا/' )
				: array( 'Home' => '/en/', 'About' => '/en/about-us/', 'Services' => '/en/services/', 'Contact' => '/en/contact-us/' ),
		),

		/* ---- 1 · HERO ----------------------------------------------------- */
		'hero' => array(
			'label' => $ar ? 'وكالتك، مدينتك' : 'YOUR AGENCY, YOUR CITY',
			'lines' => $ar
				? array( 'نموٌّ يبدأ', 'بفكرةٍ واضحة.' )
				: array( 'Growth that starts', 'with a clear idea.' ),
			'lead'  => $ar
				? 'نص تجريبي يصف ما تفعله وكالتك ولمن. استبدله بصياغتك الخاصة قبل الإطلاق.'
				: 'Demo copy describing what your agency does and for whom. Replace it with your own wording before launch.',
			'cta'   => $ar ? 'احجز استشارة مجانية' : 'Book a free consultation',
			'cta2'  => $ar ? 'شاهد الأعمال' : 'See the work',
		),

		/* QX_PLACEHOLDER: demo figures. Replace with real, sourced numbers or remove. */
		'stats' => $ar ? array(
			array( 'n' => '00+', 'cap' => 'عدد عملائك' ),
			array( 'n' => '00M', 'cap' => 'حجم وصولك السنوي' ),
			array( 'n' => '0.0x', 'cap' => 'مؤشر العائد لديك', 'accent' => true ),
			array( 'n' => '00%', 'cap' => 'نسبة استمرار العملاء' ),
		) : array(
			array( 'n' => '00+', 'cap' => 'Your client count' ),
			array( 'n' => '00M', 'cap' => 'Your annual reach' ),
			array( 'n' => '0.0x', 'cap' => 'Your return metric', 'accent' => true ),
			array( 'n' => '00%', 'cap' => 'Your retention rate' ),
		),

		/**
		 * The three stage labels that cross-fade inside the scroll-driven 3D hero, one per
		 * phase of the transformation. They mirror the Process section further down the page,
		 * stated in a word each so they read at a glance while moving.
		 */
		'heroPhases' => $ar ? array(
			array( 'n' => '01', 'label' => 'اكتشاف' ),
			array( 'n' => '02', 'label' => 'تصميم' ),
			array( 'n' => '03', 'label' => 'إطلاق' ),
		) : array(
			array( 'n' => '01', 'label' => 'DISCOVER' ),
			array( 'n' => '02', 'label' => 'DESIGN' ),
			array( 'n' => '03', 'label' => 'LAUNCH' ),
		),

		/* ---- 2 · ABOUT TEASER (charcoal) ---------------------------------- */
		'about' => array(
			'label' => $ar ? 'من نحن' : 'ABOUT',
			'lines' => $ar
				? array( 'سطرٌ تجريبي عن فلسفتك،', 'استبدله بقصتك.' )
				: array( 'A line about your philosophy.', 'Replace it with your story.' ),
			'body'  => $ar
				? 'فقرة تجريبية قصيرة تعرّف بفريقك وطريقة عملك وما يميّزك.'
				: 'A short demo paragraph introducing your team, the way you work and what sets you apart.',
			'link'  => $ar ? 'اعرف قصتنا ←' : 'Our story →',
		),

		/* ---- 3 · SERVICES ------------------------------------------------- */
		'services' => array(
			'label' => $ar ? 'خدماتنا' : 'SERVICES',
			'lines' => $ar
				? array( 'خدماتٌ متكاملة', 'تعمل معًا.' )
				: array( 'Services built', 'to work together.' ),
			'link'  => $ar ? 'اكتشف جميع الخدمات ←' : 'All services →',
			// Point each href at the matching page on your own site.
			'items' => array(
				array(
					'n'    => '01',
					'name' => $ar ? 'الاستراتيجية التسويقية' : 'Marketing Strategy',
					'meta' => $ar ? 'بحث · تموضع · خارطة طريق' : 'Research · Positioning · Roadmap',
					'href' => $ar ? '/خدماتنا/' : '/en/services/',
				),
				array(
					'n'    => '02',
					'name' => $ar ? 'الهوية والعلامة التجارية' : 'Brand & Identity',
					'meta' => $ar ? 'هوية · إرشادات · نبرة صوت' : 'Identity · Guidelines · Voice',
					'href' => $ar ? '/خدماتنا/' : '/en/services/',
				),
				array(
					'n'    => '03',
					'name' => $ar ? 'الحملات الإعلانية' : 'Advertising Campaigns',
					'meta' => $ar ? 'استراتيجية · شراء إعلامي · تقارير' : 'Strategy · Media buying · Reporting',
					'href' => $ar ? '/خدماتنا/' : '/en/services/',
				),
				array(
					'n'    => '04',
					'name' => $ar ? 'المحتوى والإبداع' : 'Content & Creative',
					'meta' => $ar ? 'تقويم · كتابة · إنتاج' : 'Calendars · Copy · Production',
					'href' => $ar ? '/خدماتنا/' : '/en/services/',
				),
				array(
					'n'    => '05',
					'name' => $ar ? 'تحسين محركات البحث' : 'Search & SEO',
					'meta' => $ar ? 'سيو تقني · محتوى · موثوقية' : 'Technical SEO · Content · Authority',
					'href' => $ar ? '/خدماتنا/' : '/en/services/',
				),
				array(
					'n'    => '06',
					'name' => $ar ? 'وسائل التواصل الاجتماعي' : 'Social Media',
					'meta' => $ar ? 'إدارة · مجتمع · تقارير' : 'Management · Community · Reporting',
					'href' => $ar ? '/خدماتنا/' : '/en/services/',
				),
			),
		),

		/* ---- 4 · CASE STUDIES (burgundy) ---------------------------------- */
		'cases' => array(
			'label' => $ar ? 'قصص نجاح' : 'CASE STUDIES',
			'title' => $ar ? 'مكان نتائجك.' : 'Your results go here.',
			'cta'   => $ar ? 'انضم إلى قائمة عملائنا' : 'Join our client list',
			/* QX_PLACEHOLDER: demo clients and results. Replace with documented work, or remove. */
			'items' => $ar ? array(
				array(
					'client' => 'CLIENT ONE', 'sector' => 'قطاع تجريبي',
					'metric' => '0.0x', 'cap' => 'استبدل بنتيجة موثّقة',
					'subs'   => array( 'مؤشر أ', 'مؤشر ب' ),
					'note'   => 'جملة واحدة عن النتيجة.',
				),
				array(
					'client' => 'CLIENT TWO', 'sector' => 'قطاع تجريبي',
					'metric' => '+00%', 'cap' => 'استبدل بنتيجة موثّقة',
					'subs'   => array( 'مؤشر أ', 'مؤشر ب' ),
					'note'   => 'جملة واحدة عن النتيجة.',
				),
				array(
					'client' => 'CLIENT THREE', 'sector' => 'قطاع تجريبي',
					'metric' => '+00%', 'cap' => 'استبدل بنتيجة موثّقة',
					'subs'   => array( 'مؤشر أ', 'مؤشر ب' ),
					'note'   => 'جملة واحدة عن النتيجة.',
				),
			) : array(
				array(
					'client' => 'CLIENT ONE', 'sector' => 'Demo sector',
					'metric' => '0.0x', 'cap' => 'Replace with a documented result',
					'subs'   => array( 'Metric A', 'Metric B' ),
					'note'   => 'One sentence on the outcome.',
				),
				array(
					'client' => 'CLIENT TWO', 'sector' => 'Demo sector',
					'metric' => '+00%', 'cap' => 'Replace with a documented result',
					'subs'   => array( 'Metric A', 'Metric B' ),
					'note'   => 'One sentence on the outcome.',
				),
				array(
					'client' => 'CLIENT THREE', 'sector' => 'Demo sector',
					'metric' => '+00%', 'cap' => 'Replace with a documented result',
					'subs'   => array( 'Metric A', 'Metric B' ),
					'note'   => 'One sentence on the outcome.',
				),
			),
		),

		/* ---- 5 · CLIENT STRIP --------------------------------------------- */
		'clients' => array(
			'label' => $ar ? 'علاماتٌ عملنا معها' : 'BRANDS WE HAVE WORKED WITH',
			/* QX_PLACEHOLDER: list only clients who have agreed to be named. */
			'names' => array( 'CLIENT ONE', 'CLIENT TWO', 'CLIENT THREE', 'CLIENT FOUR', 'CLIENT FIVE' ),
		),

		/* ---- 6 · PROCESS -------------------------------------------------- */
		'process' => array(
			'label' => $ar ? 'آلية العمل' : 'HOW WE WORK',
			'lines' => $ar
				? array( 'من الفكرة إلى الأثر', 'في ثلاث مراحل.' )
				: array( 'From idea to impact', 'in three phases.' ),
			'steps' => $ar ? array(
				array( 'n' => '01', 'title' => 'اكتشاف', 'points' => array( 'نفهم السوق', 'نصل إلى الرؤية المهمة', 'نربطها بخطة واضحة' ) ),
				array( 'n' => '02', 'title' => 'تصميم', 'points' => array( 'نصوغ الهوية والرسالة', 'نخطط للقنوات', 'نجهّز كل التفاصيل' ) ),
				array( 'n' => '03', 'title' => 'إطلاق', 'points' => array( 'ننطلق بعناية', 'نقيس ونحسّن', 'نحوّل النمو إلى عادة' ) ),
			) : array(
				array( 'n' => '01', 'title' => 'Discover', 'points' => array( 'Understand the market', 'Find the insight that matters', 'Connect it to a plan' ) ),
				array( 'n' => '02', 'title' => 'Design', 'points' => array( 'Shape the identity and message', 'Plan the channels', 'Prepare every detail' ) ),
				array( 'n' => '03', 'title' => 'Launch', 'points' => array( 'Go live with care', 'Measure and refine', 'Make growth a habit' ) ),
			),
		),

		/* ---- 7 · TESTIMONIAL (charcoal) ----------------------------------- */
		/* QX_PLACEHOLDER: use a real quote with the client's written permission, or delete
		   the section. List only partner badges the business actually holds. */
		'testimonial' => array(
			'quote'  => $ar
				? 'مكان لاقتباس حقيقي من عميل، يُنشر بعد موافقته الكتابية.'
				: 'A place for a real client quote, published with their written permission.',
			'by'     => $ar ? 'الاسم، المنصب في الشركة' : 'NAME, ROLE AT COMPANY',
			'badges' => array( 'PARTNER BADGE ONE', 'PARTNER BADGE TWO', 'PARTNER BADGE THREE', 'PARTNER BADGE FOUR' ),
		),

		/* ---- 8 · FINAL CTA ------------------------------------------------ */
		'cta' => array(
			'label' => $ar ? 'ابدأ الآن' : 'START NOW',
			'title' => $ar ? 'جاهز للخطوة التالية؟' : 'Ready for the next step?',
			'body'  => $ar
				? 'أخبرنا أين تريد أن تصل، ونرسم الطريق معك.'
				: 'Tell us where you want to be, and we will draw the road with you.',
			'wa'    => $ar ? 'ابدأ عبر واتساب' : 'Start on WhatsApp',
		),

		/* ---- FOOTER ------------------------------------------------------- */
		'footer' => array(
			'tag'          => $ar
				? 'سطر تعريفي قصير عن وكالتك.'
				: 'A short line about your agency.',
			'menuLabel'    => $ar ? 'القائمة' : 'MENU',
			'contactLabel' => $ar ? 'تواصل' : 'CONTACT',
			// Derived from the QX_WHATSAPP constant, so there is one number to maintain.
			'phone'        => '+' . QX_WHATSAPP,
			'name'         => get_bloginfo( 'name' ),
		),

		/* ---- ABOUT PAGE ----------------------------------------------------- */
		'aboutPage' => array(
			'label' => $ar ? 'من نحن' : 'ABOUT US',
			'lines' => $ar
				? array( 'فريقٌ صغير،', 'ومعايير عالية.' )
				: array( 'A small team,', 'a high standard.' ),
			'lead'  => $ar
				? 'فقرة تجريبية تعرّف بالوكالة ومن تخدمهم. استبدلها بقصتك الحقيقية.'
				: 'A demo paragraph introducing the agency and who it serves. Replace it with your real story.',

			'story' => array(
				'label' => $ar ? 'قصتنا' : 'OUR STORY',
				'lines' => $ar
					? array( 'من مشروعٍ أول', 'إلى استوديو.' )
					: array( 'From a first project', 'to a studio.' ),
				'paras' => $ar ? array(
					'فقرة تجريبية أولى تحكي كيف بدأت الوكالة وما الذي دفعها إلى البدء.',
					'فقرة تجريبية ثانية تصف أين وصلت اليوم، وما الذي لم يتغيّر منذ البداية.',
				) : array(
					'A first demo paragraph telling how the agency began and what made it start.',
					'A second demo paragraph describing where it is today, and what has not changed since the beginning.',
				),
			),

			'team' => array(
				'label' => $ar ? 'الفريق' : 'THE TEAM',
				'lines' => $ar
					? array( 'مهاراتٌ متعددة،', 'معيارٌ واحد.' )
					: array( 'Many skills,', 'one standard.' ),
				'body'  => $ar
					? 'استراتيجيون ومصممون ومحللون يعملون كفريقٍ واحد على كل حساب.'
					: 'Strategists, designers and analysts working as one team on every account.',
			),

			'values' => array(
				'label' => $ar ? 'قيمنا' : 'OUR VALUES',
				'items' => $ar ? array(
					array( 'n' => '01', 'title' => 'الأثر قبل الضجيج', 'body' => 'نقيس النجاح بما يتغيّر في أرقامك لا بما يُقال عنها.' ),
					array( 'n' => '02', 'title' => 'الدقة قبل السرعة', 'body' => 'التفاصيل هي الفرق بين عملٍ يُنسى وعملٍ يُذكر.' ),
					array( 'n' => '03', 'title' => 'الشراكة قبل الصفقة', 'body' => 'ننمو حين ينمو عملاؤنا، ونبني علاقات تدوم.' ),
					array( 'n' => '04', 'title' => 'الشفافية دائمًا', 'body' => 'تقارير واضحة وأرقام صادقة، حتى حين لا تكون في صالحنا.' ),
				) : array(
					array( 'n' => '01', 'title' => 'Impact over noise', 'body' => 'We measure success by what changes in your numbers, not by what is said about them.' ),
					array( 'n' => '02', 'title' => 'Precision over speed', 'body' => 'Details are the difference between work that is forgotten and work that is remembered.' ),
					array( 'n' => '03', 'title' => 'Partnership over deals', 'body' => 'We grow when our clients grow, and we build relationships that last.' ),
					array( 'n' => '04', 'title' => 'Transparency, always', 'body' => 'Clear reports and honest numbers, even when they are not in our favour.' ),
				),
			),

			/* QX_PLACEHOLDER: founding year, team size, client count and sectors are
			   facts about your business. Fill in the real ones or remove the row. */
			'stats' => $ar ? array(
				array( 'n' => '0000', 'cap' => 'سنة التأسيس' ),
				array( 'n' => '00', 'cap' => 'عدد أفراد الفريق' ),
				array( 'n' => '00+', 'cap' => 'عدد العملاء' ),
				array( 'n' => '0', 'cap' => 'قطاعات نعمل فيها' ),
			) : array(
				array( 'n' => '0000', 'cap' => 'Year founded' ),
				array( 'n' => '00', 'cap' => 'People on the team' ),
				array( 'n' => '00+', 'cap' => 'Clients served' ),
				array( 'n' => '0', 'cap' => 'Sectors we work in' ),
			),

			'cta' => $ar ? 'تعرّف علينا عن قرب.' : 'Get to know us properly.',
		),

		/* ---- SERVICES PAGE -------------------------------------------------- */
		'servicesPage' => array(
			'label' => $ar ? 'خدماتنا' : 'SERVICES',
			'lines' => $ar
				? array( 'خدماتٌ متكاملة،', 'لا حملاتٌ متفرقة.' )
				: array( 'Integrated services,', 'not scattered campaigns.' ),
			'lead'  => $ar
				? 'كل خدمة تُصمَّم لتعمل مع الأخريات: استراتيجية تُغذّي الإبداع، وإبداعٌ يُغذّي الأداء، وأداءٌ يُقاس ويتحسّن باستمرار.'
				: 'Every service is built to feed the others: strategy feeds creative, creative feeds performance, and performance is measured and refined continuously.',

			'items' => $ar ? array(
				array(
					'n' => '01', 'name' => 'الاستراتيجية التسويقية',
					'body' => 'وصف تجريبي للخدمة: ما الذي تفعله، ولمن، وما الذي يحصل عليه العميل في النهاية.',
					'points' => array( 'أبحاث السوق والجمهور', 'تحليل المنافسين', 'تموضع العلامة التجارية', 'خارطة طريق بمؤشرات أداء' ),
				),
				array(
					'n' => '02', 'name' => 'الهوية والعلامة التجارية',
					'body' => 'وصف تجريبي للخدمة: ما الذي تفعله، ولمن، وما الذي يحصل عليه العميل في النهاية.',
					'points' => array( 'الهوية البصرية', 'دليل استخدام العلامة', 'نبرة الصوت والرسائل', 'تطبيقات الهوية' ),
				),
				array(
					'n' => '03', 'name' => 'الحملات الإعلانية',
					'body' => 'وصف تجريبي للخدمة: ما الذي تفعله، ولمن، وما الذي يحصل عليه العميل في النهاية.',
					'points' => array( 'استراتيجية الحملة والميزانية', 'الشراء الإعلامي', 'تحسين الأداء', 'تقارير دورية' ),
				),
				array(
					'n' => '04', 'name' => 'المحتوى والإبداع',
					'body' => 'وصف تجريبي للخدمة: ما الذي تفعله، ولمن، وما الذي يحصل عليه العميل في النهاية.',
					'points' => array( 'تقويم محتوى', 'كتابة إعلانية', 'إنتاج بصري', 'تحسين التفاعل' ),
				),
				array(
					'n' => '05', 'name' => 'تحسين محركات البحث',
					'body' => 'وصف تجريبي للخدمة: ما الذي تفعله، ولمن، وما الذي يحصل عليه العميل في النهاية.',
					'points' => array( 'تدقيق تقني', 'استراتيجية كلمات مفتاحية', 'محتوى للبحث', 'بناء الروابط' ),
				),
				array(
					'n' => '06', 'name' => 'وسائل التواصل الاجتماعي',
					'body' => 'وصف تجريبي للخدمة: ما الذي تفعله، ولمن، وما الذي يحصل عليه العميل في النهاية.',
					'points' => array( 'إدارة يومية', 'بناء المجتمع', 'رصد وتحليل', 'إدارة السمعة' ),
				),
			) : array(
				array(
					'n' => '01', 'name' => 'Marketing Strategy',
					'body' => 'Demo description of the service: what it does, for whom, and what the client ends up with.',
					'points' => array( 'Market & audience research', 'Competitor analysis', 'Brand positioning', 'Roadmap with KPIs' ),
				),
				array(
					'n' => '02', 'name' => 'Brand & Identity',
					'body' => 'Demo description of the service: what it does, for whom, and what the client ends up with.',
					'points' => array( 'Visual identity', 'Brand guidelines', 'Voice & messaging', 'Identity applications' ),
				),
				array(
					'n' => '03', 'name' => 'Advertising Campaigns',
					'body' => 'Demo description of the service: what it does, for whom, and what the client ends up with.',
					'points' => array( 'Campaign strategy & budget', 'Media buying', 'Performance optimisation', 'Regular reporting' ),
				),
				array(
					'n' => '04', 'name' => 'Content & Creative',
					'body' => 'Demo description of the service: what it does, for whom, and what the client ends up with.',
					'points' => array( 'Content calendar', 'Copywriting', 'Visual production', 'Engagement optimisation' ),
				),
				array(
					'n' => '05', 'name' => 'Search & SEO',
					'body' => 'Demo description of the service: what it does, for whom, and what the client ends up with.',
					'points' => array( 'Technical audit', 'Keyword strategy', 'Content for search', 'Link building' ),
				),
				array(
					'n' => '06', 'name' => 'Social Media',
					'body' => 'Demo description of the service: what it does, for whom, and what the client ends up with.',
					'points' => array( 'Daily management', 'Community building', 'Monitoring & analysis', 'Reputation management' ),
				),
			),

			'closing' => array(
				'title' => $ar ? 'غير متأكد من أين تبدأ؟' : 'Not sure where to start?',
				'body'  => $ar
					? 'احجز استشارة مجانية ونرشدك إلى الخدمة المناسبة.'
					: 'Book a free consultation and we will point you to the right service.',
			),
		),

		/* ---- CONTACT PAGE --------------------------------------------------- */
		'contact' => array(
			'label' => $ar ? 'تواصل معنا' : 'CONTACT',
			'title' => $ar ? 'لنبدأ الحكاية.' : 'Let’s begin the story.',
			'body'  => $ar
				? 'أخبرنا عن مشروعك وأين تريد أن تصل، ونعود إليك سريعًا.'
				: 'Tell us about your project and where you want to be, and we will reply promptly.',
			'fields' => $ar
				? array( 'name' => 'الاسم', 'phone' => 'رقم الجوال', 'topic' => 'ما الذي تبحث عنه؟', 'message' => 'رسالتك', 'submit' => 'أرسل عبر واتساب' )
				: array( 'name' => 'NAME', 'phone' => 'PHONE', 'topic' => 'WHAT ARE YOU LOOKING FOR?', 'message' => 'YOUR MESSAGE', 'submit' => 'SEND VIA WHATSAPP' ),
			'topics' => $ar
				? array( 'استشارة عامة', 'استراتيجية تسويقية', 'هوية وعلامة تجارية', 'حملات إعلانية', 'محتوى وإبداع', 'تحسين محركات البحث', 'وسائل التواصل الاجتماعي' )
				: array( 'General consultation', 'Marketing strategy', 'Brand & identity', 'Advertising campaigns', 'Content & creative', 'Search & SEO', 'Social media' ),
			'hoursLabel' => $ar ? 'أوقات العمل' : 'HOURS',
			'hours'      => $ar ? 'الأحد إلى الخميس، 9 صباحًا حتى 5 مساءً' : 'Sunday to Thursday, 9am to 5pm',
			'locLabel'   => $ar ? 'الموقع' : 'LOCATION',
			// Derived from the QX_CITY_AR / QX_CITY_EN constants.
			'location'   => $ar ? QX_CITY_AR : QX_CITY_EN,
			'waLabel'    => 'WHATSAPP',
			'emailLabel' => 'EMAIL',
		),

		/* ---- JOURNAL -------------------------------------------------------- */
		'journal' => array(
			'label' => $ar ? 'المجلة' : 'JOURNAL',
			'title' => $ar ? 'نكتب عمّا نعمله.' : 'We write about the work.',
			'body'  => $ar
				? 'مقالات عن التسويق والعلامات التجارية والنمو.'
				: 'Writing on marketing, brands and growth.',
			'more'  => $ar ? 'كل المقالات ←' : 'All articles →',
		),
	);
}
