# Редактор настроек

Библиотека для обеспечения работы серверной части редактора [Giftd Editor](https://npmjs.com/package/giftd-editor).

Внимание: в этом документе используются примеры, основанные на фреймворке Laravel, но общие моменты, относящиеся только к пакету, могут быть использованы с любым другим фреймворком.

- [Настройки ресурсов (моделей)](#resource-settings)
    - [Источник настроек](#resource-settings-source)
    - [Интерфейс `HasSettings`](#resource-hassettings-interface)
- [Создание новых кастомизаций](#create-customizations)
    - [Список редактируемых элементов](#customization-editables-list)
    - [Подготовка данных для шаблона](#customization-building-data)
    - [Отображение данных в шаблонах](#customization-displaying-data)
    - [Соответствие элементов и настроек](#customization-settings-map)
- [Передача данных в редактор](#editor-initialization)
    - [Создание менеджера кастомизаций](#editor-customizations-manager)
    - [Предпросмотр изменений](#editor-preview-data)
    - [Сохранение изменений](#editor-save-data)
    - [Загрузка изображений](#editor-image-uploads)

<a name="resource-settings"></a>
## Настройки ресурсов (моделей)

У любой модели, которая может быть так или иначе изменена с помощью редактора, должен быть список изменяемых настроек. Этот список, как правило, должен храниться в какой-либо базе данных с привязкой к конкретному экземпляру модели.

Пакет не устанавливает никаких ограничений ни по устройству хранилища, ни по реализациям моделей.

<a name="resource-settings-source"></a>
### Источник настроек

В случае с Laravel для хранения настроек рекомендуется использовать пакет `zachleigh/laravel-property-bag` ([Github](https://github.com/zachleigh/laravel-property-bag)), для других фреймворков необходима своя реализация хранения таких настроек.

<a name="resource-hassettings-interface"></a>
### Интерфейс `HasSettings`

Для того, чтобы модель могла быть использована в редакторе, необходимо, чтобы она реализовывала интерфейс `Giftd\Editor\Contracts\HasSettings`. 

Метод `getResourceSettingsValues` должен возвращать из своего хранилища настроек массив вида `['key' => 'value']` для всех запрошенных ключей (параметр `array $settings`).

Метод `setResourceSettingsValues`, в свою очередь, должен принимать массив вида `['key' => 'value']` и сохранять его в хранилище настроек в соответствии с требованиями хранилища.

В случае использования с Laravel и `laravel-property-bag`, данный пакет предоставляет готовый трейт `Giftd\Editor\Traits\PropertyBagTrait`, который должен быть применён к каждой модели, настройки которой необходимо использовать в редакторе.

<a name="create-customizations"></a>
## Создание новых кастомизаций

Для создания новой кастомизации необходимо наследовать базовый класс `Giftd\Editor\Customization` и реализовать следующие методы:

- `public function editables()`: этот метод должен возвращать сгруппированный список редактируемых элементов страницы (см. [Список редактируемых элементов](#customization-editables-list));
- `public function build()`: этот метод должен подготавливать данные для шаблона в соответствии с текущими настройками модели.

<a name="customization-editables-list"></a>
### Список редактируемых элементов

Редактируемый элементом (`editable`) может быть как контент страницы (текст, изображение), так и отдельной настройкой, которая, например, может влиять на стиль отображения того или иного блока/элемента или вовсе быть недоступной в редактируемом контенте, а использоваться в любом другом участке кода, так или иначе связанным с редактируемым контентом (например, тема письма).

На данный момент имеется поддержка следующих типов настроек:

- Текст (`text`) - обычный текст или текст с возможностью вставки заранее заданных плейсхолдеров;
- Выпадающий список (`select`) - список, отображающий заранее заданные опции;
- Список галочек (`checkboxes`) - может использоваться для включения/отключения каких-либо элементов из одного списка (например, список кнопок социальных сетей на странице);
- Colorpicker (`colorpicker`) - выбор цвета элемента;
- Изображение (`image-upload`) - загружаемое изображение.

Для упрощения процесса создания списка редактируемых элементов можно использовать класс `Giftd\Editor\Editable`, вызвать инициализацию которого можно с помощью метода `Customization::editable(string $name, string $type, string $title = null, $value = null, array $attributes = [])`.

Этот метод вернёт новый экземпляр `Editable`, который можно настраивать с помощью вызовов методов по цепочке.

Т.к. редактируемые элементы должны быть сгруппированы, для создания новой группы можно использовать метод `Customization::group(string $id, string $title, \Closure $settingsFactory)`, где первым параметром необходимо указать уникальный ID группы, вторым - заголовок группы и третьим - замыкание, возвращающее список настроек этой группы.

Пример создания двух групп с несколькими настройками в каждой:

    <?php

    namespace App\Customizations;

    use Giftd\Editor\Customization;

    class WelcomeEmailCustomization extends Customization
    {
        public function editables()
        {
            return [
                'texts' => $this->group('texts-settings', 'Тексты письма', function () {
                    return [
                        $this->editable('heading', 'text', 'Заголовок письма')
                            ->withValue($this->setting('welcome_email_heading')) // текущее значение настройки модели
                            ->asInline(), // текст редактируется через `contenteditable`

                        $this->editable('intro', 'text', 'Вводный текст')
                            ->withValue($this->setting('welcome_email_intro'))
                            ->placeholder('user', 'Имя пользователя', 'John Doe')

                    ];
                }),
                'elements' => $this->group('elements-settings', 'Элементы письма', function () {
                    return [
                        $this->editable('display_social_icons', 'select', 'Отображать кнопки соц. сетей?')
                            ->withValue($this->setting('welcome_email_display_social_icons'))
                            ->withOptions([0 => 'Нет', 1 => 'Да']),

                        $this->editable('email_logo', 'image-upload', 'Логотип емейла')
                            ->withValue($this->setting('welcome_email_logo'))
                            ->uploadTo(url('/uploads/image')) // URL для загрузки нового изображения
                            ->withHeaders(['X-CSRF-Token' => 'secret']), // список заголовков, которые должны быть отправлены вместе с запросом загрузки изображения
                    ];
                })
            ];
        }
    }

<a href="customization-building-data"></a>
### Подготовка данных для шаблона

Приведенный выше список редактируемых элементов необходим для JS-версии редактора - на основе этих данных он строит интерфейс для их редактирования.

Для того, чтобы контент всегда отображался корректно и в соответствии с текущими значениями настроек, их необходимо подготавливать для каждого отображения шаблона контента. Процесс сборки данных не должен ничем отличаться как для начальной загрузки редактора, для предпросмотра данных, так и для использования шаблона в рабочем режиме (например, во время отправки настоящего письма пользователю).

Подготовка данных должна происходить в методе `build` каждой кастомизации.

В этом методе можно присваивать ключи массиву `protected $data`. Уже в шаблоне можно использовать метод `get` для доступа к данным, сохраненным таким образом.

Процесс сборки данных уникален для каждой кастомизации (у пакета нет явных ограничений на то, что может в этом методе происходить), но для удобства вы можете использовать несколько вспомогательных методов. Рассмотрим пример подготовки данных для письма выше.

    <?php

    namespace App\Customizations;

    use App\User;
    use Giftd\Editor\Customization;

    class WelcomeEmailCustomization extends Customization
    {
        protected $user;

        public function __construct(User $user)
        {
            $this->user = $user;
        }

        public function build()
        {
            // Т.к. заголовок письма является обычным текстом
            // без плейсхолдеров, достаточно вызвать метод
            // setting для получения текущего значения. 
            $this->data['heading'] = $this->setting('welcome_email_heading');

            // Вступительный текст письма, однаок, использует плейсхолдер
            // `user`, куда должно подставиться имя пользователя. Для
            // этого можно вызвать метод `settingWithPlaceholders`.
            $this->data['intro'] = $this->settingWithPlaceholders('welcome_email_intro', ['user' => $this->user->name]);

            $this->data['display_social_icons'] = intval($this->setting('welcome_email_display_social_icons')) === true;
            $this->data['logo'] = $this->setting('welcome_email_logo');
        }
    }

<a href="customization-displaying-data"></a>
### Отображение данных в шаблонах

Для обеспечения корректной работы шаблонов с настройками необходимо правильно подготовить данные (метод `build`). Эту задачу решает класс `Giftd\Editor\CustomizationsManager`.

Предположим, так выглядит Welcome-письмо:

    <?php

    namespace App\Mail;

    use Illuminate\Mail\Mailable;

    class WelcomeMail extends Mailable
    {
        protected $manager;
        protected $customization;

        public function __construct(CustomizationsManager $manager)
        {
            $this->manager = $manager;
            $this->customization = $manager->customization();
        }

        public function build()
        {
            return $this->view('emails.welcome', [
                'customization' => $this->customization,
            ]);
        }
    }

А так - шаблон письма:

    @extends('layouts.email')

    @section('content')
        <img src="{{ $customization->get('logo') }}" alt="Logo">
        <h1>{{ $customization->get('heading') }}</h1>
        <p>{! $customization->get('intro') }}</p>
        @if ($customization->get('display_social_icons'))
            <div class="social-icons">
                ...
            </div>
        @endif
    @endsection

Для того, чтобы передать `CustomizationsManager` в конструктор `WelcomeEmail`, необходимо сделать следующее:

1. Создать экземпляр класса `Giftd\Editor\SettingsManager`, передав в его конструктор модель с настройками;
2. Создать экземпляр класса `WelcomeEmailCustomization`, передав в его конструктор пользователя, которому отправляется письмо;
3. Создать экземпляр класса `Giftd\Editor\CustomizationsManager`, передав в его конструктор инстанс настроек и кастомизации;
4. Вызвать метод `buildData` менеджера кастомизации для подготовки данных.

Следующий пример показывает задачу отправки письма новому пользователю компании:

    <?php

    namespace App\Jobs;

    use App\User;
    use App\Company;
    use App\Customizations\WelcomeEmailCustomization;
    use App\Mail\WelcomeEmail;
    use Giftd\Editor\CustomizationsManager;
    use Giftd\Editor\SettingsManager;
    use Illuminate\Bus\Queueable;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Illuminate\Support\Facades\Mail;

    class SendWelcomeEmail implements ShouldQueue
    {
        use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        protected $user;
        protected $company;

        public function __construct(User $user, Company $company)
        {
            // Пользователь, которому будет отправлено письмо.
            $this->user = $user;

            // Компания, настройки которой будут редактироваться.
            $this->company = $company;
        }

        public function handle()
        {
            $settings = new SettingsManager($this->company);
            $customization = new WelcomeEmailCustomization($this->user);
            $manager = new CustomizationsManager($customization, $settings);
            $manager->buildData();

            Mail::to($this->user)->send(new WelcomeEmail($manager));
        }
    }

Отправка же этого письма может выглядеть примерно так:

    use App\Jobs\SendWelcomeEmail;

    dispatch(new SendWelcomeEmail($user, $company));

<a name="customization-settings-map"></a>

### Соответствие элементов и настроек

В приведенном примере метода `editables` ключи настроек (`heading`, `intro`) отличаются от названий настроек модели (`welcome_email_heading`, `welcome_email_intro` соотв.). Для того, чтобы предпросмотр и сохранение данных работали корректно, необходимо, чтобы эти настройки совпадали.

В случае, если совпадение настроек невозможно или неудобно, можно переопределить метод `Customization::settingsMap` и вернуть из него массив соответствий элементов и настроек, ключами которых будут названия настроек из списка `editables`, а значениями - названия настроек модели.

    <?php

    namespace App\Customizations;

    use App\User;
    use Giftd\Editor\Customization;

    class WelcomeEmailCustomization extends Customization
    {
        public function settingsMap()
        {
            return [
                'heading' => 'welcome_email_heading',
                'intro' => 'welcome_email_intro',
                'display_social_icons' => 'welcome_email_display_social_icons',
                'logo' => 'welcome_email_logo',
            ];
        }
    }

Во время предпросмотра или сохранения данных менеджер кастомизаций будет вызывать этот метод и заменять все указанные ключи.

<a name="editor-initialization"></a>
## Передача данных в редактор

Т.к. JS-версия редактора требует список редактируемых элементов для своей инициализации, её необходимо подготовить перед отправкой в шаблон.

<a name="editor-customizations-manager"></a>
### Создание менеджера кастомизаций

Для управления кастомизациями всё так же используется класс `CustomizationsManager`.

    <?php

    namespace App\Http\Controllers;

    use App\Company;
    use App\User;
    use App\Customizations\WelcomeEmailCustomization;
    use Giftd\Editor\CustomizationsManager;
    use Giftd\Editor\SettingsManager;

    class EditorController extends Controller
    {
        public function index()
        {
            // Компания, настройки которой будут редактироваться.
            $company = Company::find(1);

            // Пользователь для корректного отображения данных.
            $user = User::find(1);

            $settings = new SettingsManager($company);
            $customization = new WelcomeEmailCustomization($user);
            $manager = new CustomizationsManager($customization, $settings);

            return view('editor', [
                'editables' => $manager->parseEditables(),
            ]);
        }
    }

Метод `CustomizationsManager::parseEditables` вызывает метод `editables` экземпляра кастомизации и трансформирует его результат в массив, необходимый JS-версии редактора. В шаблоне `editor` переменную `$editables` можно передать в настройки редактора через PHP-функцию `json_encode`:

    GiftdEditor.initEditor({
        editables: {! json_encode($editables) !},
    })

<a name="editor-preview-data"></a>
### Предпросмотр изменений

Для предпросмотра изменений редактор использует следующий алгоритм:

1. После нажатия на кнопку "Предпросмотр" в редакторе, JS-приложение выполняет отправку методом POST всех текущих значений всех настроек на URL, указанный в параметре `previewDataUrl`. Настройки передаются в поле `settings`. Обработчик по этому адресу должен выполнить сохранение полученных данных в любое удобное для него хранилище (сессия, memcached, redis и т.д.) и вернуть положительный ответ `2XX`;
2. JS-приложение, после получения положительного ответа от предыдущего шага, перенаправляет iframe контента на адрес, указанный в параметре `previewUrl`, обработчик которого должен отобразить идентичный контент, но с перезаписанными данными предпросмотра.

В методе контроллера, отвечающего за отображение предпросмотра, необходимо создать экземпляр `WelcomeEmailCustomization` и `CustomizationManager`  с единственным отличием от первичной инциализации: третьим аргументом конструктора `CustomizationsManager` необходимо передать сохраненные ранее данные предпросмотра в виде массива `['key' => 'value']`. Дальше менеджер сделает всё автоматически.

    <?php

    namespace App\Http\Controllers;

    use App\Company;
    use App\User;
    use App\Customizations\WelcomeEmailCustomization;
    use Giftd\Editor\CustomizationsManager;
    use Giftd\Editor\SettingsManager;
    use Illuminate\Http\Request;

    class EditorController extends Controller
    {
        public function savePreviewData(Request $request)
        {
            $settings = $request->input('settings', []);
            $request->session()->put('welcome_email_preview_data', $settings);

            return response('', 204);
        }

        public function preview(Request $request)
        {
            // Компания, настройки которой будут редактироваться.
            $company = Company::find(1);

            // Пользователь для корректного отображения данных.
            $user = User::find(1);

            // Данные предпросмотра, сохраненные в методе `savePreviewData`
            $previewData = $request->session()->get('welcome_email_preview_data', []);

            $settings = new SettingsManager($company);
            $customization = new WelcomeEmailCustomization($user);
            $manager = new CustomizationsManager($customization, $settings, previewData);

            return view('emails.welcome', [
                'editables' => $manager->parseEditables(),
            ]);
        }
    }

<a name="editor-save-data"></a>
### Сохранение изменений

Сохранение данных происходит практически так же, как и предпросмотр:

    <?php

    namespace App\Http\Controllers;

    use App\Company;
    use App\User;
    use App\Customizations\WelcomeEmailCustomization;
    use Giftd\Editor\CustomizationsManager;
    use Giftd\Editor\SettingsManager;
    use Illuminate\Http\Request;

    class EditorController extends Controller
    {
        public function saveSettings(Request $request)
        {
            // Значения настроек.
            $saveData = $request->input('settings', []);

            // Компания, настройки которой будут редактироваться.
            $company = Company::find(1);

            // Пользователь для корректного отображения данных.
            $user = User::find(1);

            $settings = new SettingsManager($company);
            $customization = new WelcomeEmailCustomization($user);
            $manager = new CustomizationsManager($customization, $settings, saveData);

            $manager->saveSettings();

            // Сохраненные ранее данные предпросмотра можно очистить.
            $request->session()->forget('welcome_email_preview_data');

            return response('', 204);
        }
    }

<a name="editor-image-uploads"></a>
### Загрузка изображений

Загрузка изображений производится на URL, указанный с помощью метода `Editable::uploadTo`, запрос отправляется методом POST. Если вместе с запросом необходимо отправить заголовки (например, CSRF-токен), можно вызвать метод `Editable::withHeaders` и передать туда массив вида `['header' => 'value']`.

После успешной загрузки изображения контроллер должен вернуть JSON-ответ следующего формата:

    {
        "data": {
            "src": "https://example.org/image.png"
        }
    }

Значением поля `data.src` должен быть полный URL к картинке. Именно это значение будет сохранено в настройку модели.

На данный момент эта структура ответа является обязательной, в будущем будет добавлена возможность установки произвольного формата ответа.
