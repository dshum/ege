<?php

use Moonlight\Main\Site;
use Moonlight\Main\Item;
use Moonlight\Main\Element;
use Moonlight\Main\Rubric;
use Moonlight\Properties\BaseProperty;
use Moonlight\Properties\MainProperty;
use Moonlight\Properties\OrderProperty;
use Moonlight\Properties\CheckboxProperty;
use Moonlight\Properties\DatetimeProperty;
use Moonlight\Properties\DateProperty;
use Moonlight\Properties\FloatProperty;
use Moonlight\Properties\ImageProperty;
use Moonlight\Properties\IntegerProperty;
use Moonlight\Properties\OneToOneProperty;
use Moonlight\Properties\ManyToManyProperty;
use Moonlight\Properties\PasswordProperty;
use Moonlight\Properties\RichtextProperty;
use Moonlight\Properties\TextareaProperty;
use Moonlight\Properties\TextfieldProperty;
use Moonlight\Properties\PluginProperty;
use Moonlight\Properties\VirtualProperty;

$topics = \App\Topic::orderBy('order')->get();

$site = \App::make('site');

$site->
    
    /*
	 * Раздел сайта
	 */

	addItem(
		Item::create('App\Section')->
		setTitle('Раздел сайта')->
		setRoot(true)->
        setCreate(true)->
		setElementPermissions(true)->
		addOrder()->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')->
			setRequired(true)
		)->
		addProperty(
			TextfieldProperty::create('url')->
			setTitle('Адрес страницы')->
            setRequired(true)->
			addRule('regex:/^[a-z0-9\-]+$/i', 'Допускаются латинские буквы, цифры и дефис.')
		)->
		addProperty(
			TextfieldProperty::create('title')->
			setTitle('Title')->
			setShow(true)
		)->
		addProperty(
			TextfieldProperty::create('h1')->
			setTitle('H1')->
			setShow(true)
		)->
		addProperty(
			TextfieldProperty::create('meta_keywords')->
			setTitle('META Keywords')->
			setShow(true)
		)->
		addProperty(
			TextareaProperty::create('meta_description')->
			setTitle('META Description')->
			setShow(true)
		)->
		addProperty(
			RichtextProperty::create('fullcontent')->
			setTitle('Текст раздела')
		)->
        addProperty(
			OneToOneProperty::create('section_id')->
			setTitle('Раздел сайта')->
			setRelatedClass('App\Section')->
			setParent(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Служебный раздел
	 */

	addItem(
		Item::create('App\ServiceSection')->
		setTitle('Служебный раздел')->
		setRoot(true)->
        setCreate(true)->
		setElementPermissions(true)->
		addOrder()->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')->
			setRequired(true)
		)->
		addProperty(
			OneToOneProperty::create('service_section_id')->
			setTitle('Служебный раздел')->
			setRelatedClass('App\ServiceSection')->
			setParent(true)->
            setOpenItem(true)->
			setShow(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Настройки сайта
	 */

	addItem(
		Item::create('App\SiteSettings')->
		setTitle('Настройки сайта')->
		setRoot(true)->
		setCreate(false)->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')->
			setRequired(true)
		)->
		addProperty(
			TextfieldProperty::create('title')->
			setTitle('Title')->
			setRequired(true)->
			setShow(true)
		)->
        addProperty(
			TextfieldProperty::create('meta_keywords')->
			setTitle('META Keywords')->
			setShow(true)
		)->
		addProperty(
			TextareaProperty::create('meta_description')->
			setTitle('META Description')->
			setShow(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->
    
    /*
	 * Ученик
	 */

	addItem(
		Item::create('App\User')->
		setTitle('Ученик')->
        setCreate(true)->
        setPerPage(10)->
        addOrderBy('created_at', 'desc')->
		addProperty(
			MainProperty::create('email')->
			setTitle('E-mail')->
			addRule('email', 'Некорректный адрес электронной почты')->
			setRequired(true)
		)->
		addProperty(
			PasswordProperty::create('password')->
			setTitle('Пароль')
		)->
        addProperty(
			TextfieldProperty::create('first_name')->
			setTitle('Имя')->
			setRequired(true)->
			setShow(true)
		)->
        addProperty(
			TextfieldProperty::create('last_name')->
			setTitle('Фамилия')->
			setRequired(true)->
			setShow(true)
		)->
		addProperty(
			ImageProperty::create('photo')->
			setTitle('Фотография')->
			setResize(200, 200, 100)->
			setShow(true)
		)->
        addProperty(
			CheckboxProperty::create('activated')->
			setTitle('Активирован')->
			setShow(true)
		)->
        addProperty(
			CheckboxProperty::create('banned')->
			setTitle('Заблокирован')->
			setShow(true)
		)->
        addProperty(
			OneToOneProperty::create('service_section_id')->
			setTitle('Служебный раздел')->
			setRelatedClass('App\ServiceSection')->
			setRequired(true)->
			setParent(true)->
            setOpenItem(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Предмет
	 */

	addItem(
		Item::create('App\Subject')->
		setTitle('Предмет')->
        setCreate(true)->
		addOrder()->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')->
			setRequired(true)
		)->
		addProperty(
			CheckboxProperty::create('hidden')->
			setTitle('Скрыто')->
			setShow(true)
		)->
		addProperty(
			OneToOneProperty::create('service_section_id')->
			setTitle('Служебный раздел')->
			setRelatedClass('App\ServiceSection')->
			setRequired(true)->
			setParent(true)->
            setOpenItem(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Тема
	 */

	addItem(
		Item::create('App\Topic')->
		setTitle('Тема')->
        setCreate(true)->
		addOrder()->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')->
			setRequired(true)
		)->
		addProperty(
			CheckboxProperty::create('hidden')->
			setTitle('Скрыто')->
			setShow(true)
		)->
		addProperty(
			OneToOneProperty::create('subject_id')->
			setTitle('Предмет')->
			setRequired(true)->
			setRelatedClass('App\Subject')->
			setParent(true)->
            setOpenItem(true)->
			setShow(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Подтема
	 */

	addItem(
		Item::create('App\Subtopic')->
		setTitle('Подтема')->
        setCreate(true)->
		addOrder()->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')->
			setRequired(true)
		)->
		addProperty(
			CheckboxProperty::create('hidden')->
			setTitle('Скрыто')->
			setShow(true)
		)->
		addProperty(
			OneToOneProperty::create('topic_id')->
			setTitle('Тема')->
			setRequired(true)->
			setRelatedClass('App\Topic')->
			setParent(true)->
            setOpenItem(true)->
			setShow(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Тест
	 */

	addItem(
		Item::create('App\Test')->
		setTitle('Тест')->
        setCreate(true)->
		addOrder()->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')->
			setRequired(true)
		)->
		addProperty(
			OneToOneProperty::create('topic_id')->
			setTitle('Тема')->
			setRelatedClass('App\Topic')->
			setParent(true)->
            setOpenItem(true)->
			setShow(true)->
			setRequired(true)
		)->
		addProperty(
			OneToOneProperty::create('subtopic_id')->
			setTitle('Подтема')->
			setRelatedClass('App\Subtopic')->
            setOpenItem(true)->
			setShow(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Вопрос
	 */

	addItem(
		Item::create('App\Question')->
		setTitle('Вопрос')->
        setCreate(true)->
		addOrderBy('order')->
		addProperty(
			OrderProperty::create('order')->
			setRelatedClass('App\Test')
		)->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')
		)->
		addProperty(
			RichtextProperty::create('question')->
			setTitle('Текст вопроса')->
			setShow(true)
		)->
		addProperty(
			PluginProperty::create('answers_info')->
			setTitle('Ответы')->
			setShow(true)
		)->
		addProperty(
			RichtextProperty::create('explanation')->
			setTitle('Объяснение')
		)->
		addProperty(
			TextareaProperty::create('comments')->
			setTitle('Комментарий')
		)->
		addProperty(
			IntegerProperty::create('mark')->
			setTitle('Баллы')->
			setShow(true)
		)->
		addProperty(
			TextfieldProperty::create('answer')->
			setTitle('Ответ')
		)->
		addProperty(
			ManyToManyProperty::create('tests')->
			setTitle('Тесты')->
			setRelatedClass('App\Test')->
            setRelatedMethod('questions')->
            setOpenItem(true)->
			setShow(true)->
			setShowOrder(true)
		)->
		addProperty(
			OneToOneProperty::create('topic_id')->
			setTitle('Тема')->
			setRelatedClass('App\Topic')->
			setParent(true)->
			setRequired(true)
		)->
		addProperty(
			OneToOneProperty::create('question_type_id')->
			setTitle('Тип вопроса')->
			setRelatedClass('App\QuestionType')->
			setShow(true)->
			setRequired(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Ответ
	 */

	addItem(
		Item::create('App\Answer')->
		setTitle('Ответ')->
        setCreate(true)->
		addOrder()->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')
		)->
		addProperty(
			TextfieldProperty::create('answer')->
			setTitle('Ответ')->
			setShow(true)
		)->
		addProperty(
			CheckboxProperty::create('correct')->
			setTitle('Правильный')->
			setShow(true)
		)->
		addProperty(
			OneToOneProperty::create('question_id')->
			setTitle('Вопрос')->
			setRequired(true)->
			setRelatedClass('App\Question')->
			setParent(true)->
            setOpenItem(true)->
			setShow(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Тест ученика
	 */

	addItem(
		Item::create('App\UserTest')->
		setTitle('Тест ученика')->
		addOrderBy('created_at', 'desc')->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')
		)->
		addProperty(
			CheckboxProperty::create('complete')->
			setTitle('Завершен')->
			setShow(true)
		)->
		addProperty(
			OneToOneProperty::create('user_id')->
			setTitle('Ученик')->
			setRequired(true)->
			setRelatedClass('App\User')->
			setParent(true)->
            setOpenItem(true)->
			setShow(true)
		)->
		addProperty(
			OneToOneProperty::create('test_id')->
			setTitle('Тест')->
			setRequired(true)->
			setRelatedClass('App\Test')->
			setShow(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Вопрос ученика
	 */

	addItem(
		Item::create('App\UserQuestion')->
		setTitle('Вопрос ученика')->
		setPerPage(25)->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')
		)->
		addProperty(
			CheckboxProperty::create('correct')->
			setTitle('Правильный')->
			setShow(true)
		)->
		addProperty(
			TextfieldProperty::create('answer')->
			setTitle('Ответ')->
			setRequired(true)->
			setShow(true)
		)->
		addProperty(
			OneToOneProperty::create('user_test_id')->
			setTitle('Тест ученика')->
			setRequired(true)->
			setRelatedClass('App\UserTest')->
			setParent(true)->
            setOpenItem(true)->
			setShow(true)
		)->
		addProperty(
			OneToOneProperty::create('question_id')->
			setTitle('Вопрос')->
			setRequired(true)->
			setRelatedClass('App\Question')->
			setShow(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Ответ ученика
	 */

	addItem(
		Item::create('App\UserAnswer')->
		setTitle('Ответ ученика')->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')
		)->
		addProperty(
			OneToOneProperty::create('user_question_id')->
			setTitle('Вопрос ученика')->
			setRequired(true)->
			setRelatedClass('App\UserQuestion')->
			setParent(true)->
            setOpenItem(true)->
			setShow(true)
		)->
		addProperty(
			OneToOneProperty::create('answer_id')->
			setTitle('Ответ')->
			setRequired(true)->
			setRelatedClass('App\Answer')->
			setShow(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Тип вопроса
	 */

	addItem(
		Item::create('App\QuestionType')->
		setTitle('Тип вопроса')->
        setCreate(true)->
		addOrder()->
		addProperty(
			MainProperty::create('name')->
			setTitle('Название')->
			setRequired(true)
		)->
		addProperty(
			OneToOneProperty::create('service_section_id')->
			setTitle('Служебный раздел')->
			setRequired(true)->
			setRelatedClass('App\ServiceSection')->
			setParent(true)->
            setOpenItem(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	// addHomePlugin('App\Http\Plugins\Welcome')->
	// addBrowseFilter('App.Question', 'App\Http\Plugins\QuestionFilter')->
	
	addItemStyle('App.Question', '/css/answers.css')->
	addItemScript('App.Question', '/js/answers.js')->
	
	addBrowseStyle(env('site.loader', 'App.ServiceSection.4'), '/css/loader.css')->
	addBrowseScript(env('site.loader', 'App.ServiceSection.4'), '/js/loader.js')->
	addBrowsePlugin(env('site.loader', 'App.ServiceSection.4'), 'App\Http\Plugins\TestLoader')->

	addBrowseStyle(env('site.photo', 'App.ServiceSection.5'), '/css/photo.css')->
	addBrowseScript(env('site.photo', 'App.ServiceSection.5'), '/js/photo.js')->
	addBrowsePlugin(env('site.photo', 'App.ServiceSection.5'), 'App\Http\Plugins\PhotoLoader')->

	bind(Site::ROOT, ['App.Section', 'App.ServiceSection', 'App.SiteSettings'])->
	bind(env('site.subjects', 'App.ServiceSection.2'), 'App.Subject')->
    bind(env('site.dicts', 'App.ServiceSection.3'), 'App.ServiceSection')->
	bind(env('site.types', 'App.ServiceSection.6'), 'App.QuestionType')->
	bind(env('site.students', 'App.ServiceSection.1'), 'App.User')->
	bind('App.Subject', 'App.Topic')->
	bind('App.Topic', ['App.Subtopic', 'App.Test', 'App.Question'])->
	bind('App.Subtopic', 'App.Test')->
	bind('App.Test', 'App.Question')->
	bind('App.Question', 'App.Answer')->
	bind('App.User', 'App.UserTest')->
	bind('App.UserTest', 'App.UserQuestion')->
	bind('App.UserQuestion', 'App.UserAnswer')->

	/*
	 * Рубрики
	 */

	addRubric(
		Rubric::create('students', 'Ученики')->
		addList('App.User')
	);

	foreach ($topics as $topic) {
		$site->
		addRubric(
			Rubric::create('topic_'.$topic->id, $topic->name)->
			addElement(Element::getClassId($topic), $topic->name)->
			addList([Element::getClassId($topic) => 'App.Subtopic'])->
			addList([Element::getClassId($topic) => 'App.Test'])
		);
	}

	$site->
	addRubric(
		Rubric::create('service_sections', 'Служебные разделы')->
		addList([Site::ROOT => 'App.ServiceSection'])
	)->
	addRubric(
		Rubric::create('subjects', 'Предметы')->
		addList('App.Subject')
	)->
	addRubric(
		Rubric::create('dicts', 'Справочники')->
		addList([env('site.dicts', 'App.ServiceSection.3') => 'App.ServiceSection'])
	)->
	addRubric(
		Rubric::create('site_settings', 'Настройки сайта')->
		addList('App.SiteSettings')
	)->

	end();