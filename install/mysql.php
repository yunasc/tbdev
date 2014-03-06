<?php
/**
 * Файл объявления класса {@link CMySQL}.
 *
 * @package person
 * @subpackage sysclasses
 * 
 */

///////////////////////////////////////////////////////
/**
 * Строки возвращаются в двумерный массив, каждая строка которого -
 * ассоциативный массив вида "поле" => "значение".
 * Используется в методе {@link CMySQL::fetch_all()}.
 */
define("DB_FETCH_ALL_INDEX_KEYS",  1);

/**
 * Строки возвращаются в двумерный массив, каждая строка которого -
 * ассоциативный массив вида "поле" => "значение". Значения поля результата
 * участвуют в образовании ключей массива строк.
 * Используется в методе {@link CMySQL::fetch_all()}.
 */
define("DB_FETCH_ALL_VALUE_KEYS", 2);

/**
 * Одномерный массив вида "значение_первого_поля" => "значение_второго_поля".
 * Используется в методе {@link CMySQL::fetch_all()}.
 */
define("DB_FETCH_ALL_VECTOR", 3);

/**
 * Столбцы возвращаются как массив вида "поле" => "значение".
 * Используется в методе {@link CMySQL::fetch_row()}.
 */
define("DB_FETCH_ASSOC", MYSQL_ASSOC);

/**
 * Столбцы возвращаются как массив значений с числовыми ключами.
 * Используется в методе {@link CMySQL::fetch_row()}.
 */
define("DB_FETCH_NUM", MYSQL_NUM);
///////////////////////////////////////////////////////

/**
 * Класс для работы с СУБД MySQL.
 * 
 * Самые основные методы, которые будут
 * нужны программисту при работе с БД:
 * - {@link CMySQL::query()} - выполнение любых запросов
 * - {@link CMySQL::get_one()} - возврат значения первого столбца в первой строке результата
 * - {@link CMySQL::get_first()} - возврат первой строки результата
 * - {@link CMySQL::get_list()},
 *   {@link CMySQL::get_key_list()},
 *   {@link CMySQL::get_vector()},
 *   {@link CMySQL::fetch_all()} - получение всех строк результата запроса в виде различных структур данных
 * - {@link CMySQL::insert()} - вставка строки в таблицу
 * - {@link CMySQL::update()} - изменение строк таблицы
 * - {@link CMySQL::delete()} - удаление строк таблицы
 * 
 * Главное, что нужно понять, это использование праметризированных запросов.
 * Практически у всех методов, посылающих запрос к БД, есть аргумент $params (обычно
 * второй, после строки запроса). Он может быть как ассоциативным массивом (параметр => значение),
 * так и просто значением, в случае, когда в запросе участвует только один параметр.
 * Имена параметров (placeholders) в строке запроса обрабляются с обоих сторон знаком "%".
 * Значание параметров автоматически экранируются (см. {@link CMySQL::params_quoted()}).
 * Примеры использования:
 * 
 * <code>
 *     ...
 *     $params = array(
 *         "foo1" => "'qwerty'",
 *         "foo2" => 3
 *     );
 *     $db->query("SELECT * FROM foo WHERE foo1 = %foo1% AND foo2 = %foo2%", $params);
 *     // результирующий запрос:
 *     // SELECT * FROM foo WHERE foo1 = '\'qwerty\'' AND foo2 = '3'
 *     ...
 *     $foo_id = 12;
 *     $db->query("SELECT field FROM foo WHERE foo_id = %id%", $foo_id);
 *     // результирующий запрос:
 *     // SELECT field FROM foo WHERE foo_id = '12'
 *     ...
 * </code>
 *
 * @package person
 * @subpackage sysclasses
 */
class mysql {
// private:
/**#@+
 * @access private
 */
    /**
     * адрес сервера
     * @var string
     */
    var $_db_host;
    /**
     * порт сервера
     * @var string
     */
    var $_db_port;
    /**
     * пользователь
     * @var string
     */
    var $_db_user;
    /**
     * пароль
     * @var string
     */
    var $_db_password;
    /**
     * имена баз данных для данного подключения
     * @var array
     */
    var $_db_names;
    /**
     * кодировка баз данных для данного подключения
     * @var string
     */
    var $_charset;
    /**
     * русерс соединения
     * @var resource
     */
     
    var $_cur_db_name;
     
    var $_db_link;

    /**
     * имя файла для ведения лога ошибок
     * @var string
     */
    var $_error_log_file;
    /**
     * вести ли лог ошибок
     * @var bool
     */
    var $_error_log = false;

    var $_db_error = false;
    var $_query_log_verbose = false;
    var $_query_log_file = "";
    var $_query_log = false;
    var $_query_log_only_updates = false;

    var $_param_char = "%";
    var $_db_char = "#";

    var $_field_quote = "`";

    var $_reset_error_handler = true;
    /**
     * последний запрос к БД
     */
    var $_last_query = "";
    /**
     * Последняя ошибка
     */
    var $_last_error = "";
    /**
     * Флаг, говорящий о том, экранированы ли
     * кавычки в передаваемых в запрос параметрах или нет.
     */
    var $_params_already_quoted = false;

///////////////////////////////////////////////////////////////////////////////////
    function __connect($host, $user, $passwd, $dbname=null)
    {
        return @mysql_connect($host, $user, $passwd, true);
    }
    ///////////////////////////////////////////////////////////////////////////////
    function __select_db($db_name, $link)
    {
        return @mysql_select_db($db_name, $link);
    }
    ///////////////////////////////////////////////////////////////////////////////
    function __close($link)
    {
        return @mysql_close($link);
    }
    ///////////////////////////////////////////////////////////////////////////////
    function &__query($query, $link = null)
    {
        return @mysql_query($query, $link);
    }
    ///////////////////////////////////////////////////////////////////////////////
    function &__fetch_assoc(&$res)
    {
        return mysql_fetch_assoc($res);
    }
    ///////////////////////////////////////////////////////////////////////////////
    function &__fetch_num(&$res)
    {
        return mysql_fetch_row($res);
    }
    ///////////////////////////////////////////////////////////////////////////////
    function __error($link = null)
    {
        return @mysql_error($link);
    }
    ///////////////////////////////////////////////////////////////////////////////
    function __free_result(&$res)
    {
        return mysql_free_result($res);
    }
    ///////////////////////////////////////////////////////////////////////////////
    function __escape_string($value)
    {
        return mysql_escape_string($value); 
    }
    ///////////////////////////////////////////////////////////////////////////////
    function __data_seek(&$res, $pos)
    {
        if ($this->__num_rows($res) > 0) {
            return mysql_data_seek($res, $pos);
        } else {
            return true;
        }
    }
    ///////////////////////////////////////////////////////////////////////////////
    function __num_rows(&$res)
    {
        return mysql_num_rows($res);
    }
    ///////////////////////////////////////////////////////////////////////////////
    function __affected_rows()
    {
        return mysql_affected_rows($this->_db_link);
    }
    ///////////////////////////////////////////////////////////////////////////////
    function __log_connect($db_host, $db_user)
    {
        return "-- connect with " . $db_user . " on ". $db_host;
    }
    ///////////////////////////////////////////////////////////////////////////////
    function __log_selecting_db($db_name)
    {
        return "USE $db_name";
    }
    ///////////////////////////////////////////////////////////////////////////////
    function &__table_fields($table_name, $db_name = null)
    {
        $table_name = addslashes($table_name);
        $sql = "SHOW COLUMNS FROM `" . $table_name . "`";
        $result = $this->get_first($sql, null, $db_name);
        return $result;
    }
///////////////////////////////////////////////////////////////////////////////////
    /**
     * Функция обработки ошибок
     */
    function _dbErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() & $errno) {
            $err_text = "<br />\n";
            switch ($errno) {
                case E_USER_ERROR:
                    $err_text .= "<b>FATAL</b> [$errno] $errstr<br />\n";
                break;
                case E_USER_WARNING:
                    $err_text .= "<b>WARNING</b> [$errno] $errstr<br />\n";
                break;
                case E_NOTICE:
                case E_USER_NOTICE:
                    $err_text .= "<b>NOTICE</b> [$errno] $errstr<br />\n";
                break;
                default:
                    $err_text .= "Unkown error type: [$errno] $errstr<br />\n";
                break;
            }
            $err_text .= "<br />\n";
            if (!array_key_exists("SERVER_NAME", $_SERVER)) {
                $err_text = strip_tags($err_text);
            }
            echo $err_text;
            //if ($errno == E_USER_ERROR) {
                exit(1);
            //}
        }
    }
    ///////////////////////////////////////////////////////
    /**
     * Возвращает реальное имя БД.
     * @param string $db_name упрощенное имя 
     */
    function _get_real_db_name($db_name)
    {
        if (@array_key_exists($db_name, $this->_db_names)) {
            $db_name = $this->_db_names[$db_name];
        }
        return $db_name;
    }
    
    ///////////////////////////////////////////////////////
    function _log_query($entry, $type = "normal", $start_run_time = 0, $end_run_time = 0)
    {
        $logging = true;
        if ($this->_query_log_only_updates && !$this->_is_update_query($entry) && $type == "normal") {
            $logging = false;
        }
        if ($logging) {
            $fp = fopen($this->_query_log_file, "at");
            fputs($fp, $entry.";\n");
            if ($this->_query_log_verbose) {
                $bt = debug_backtrace();
                $i = 0;
                while ($bt[$i]["file"] == __FILE__) $i++;
                $file = $bt[$i]["file"];
                $line = $bt[$i]["line"];
                fputs($fp, "-- URI: ".$_SERVER["REQUEST_URI"]."\n");
                fputs($fp, "-- File: $file (line: $line)\n");
                $gen_time = $end_run_time - $start_run_time;
                fputs($fp, "-- Run-time: ".sprintf("%.4f", $gen_time)."\n\n");
            }
            fclose($fp);
        }
    }
    ///////////////////////////////////////////////////////////////////////////////
    function _log_connect($db_host, $db_user)
    {
        $this->_log_query($this->__log_connect($db_host, $db_user), "connect");
    }
    ///////////////////////////////////////////////////////////////////////////////
    function _log_selecting_db($db_name)
    {
        $this->_log_query($this->__log_selecting_db($db_name), "db");
    }
    ///////////////////////////////////////////////////////////////////////////////
    function _error($errstr, $errtype = E_USER_WARNING)
    {
        $this->_db_error = true;
        $bt = debug_backtrace();
        $i = 0;
        while ($bt[$i]["function"] == $bt[0]["function"] || $bt[$i]["file"] == __FILE__) {
            $i++;
        }
        $file = $bt[$i]["file"];
        $line = $bt[$i]["line"];
        $errstr = "$errstr<br />\nin file <b>$file</b> on line <b>$line</b><br />\n";
        $errstr .= "<b>Details:</b><br />\nHost: " . $this->_db_host . "<br />\nUser: " . $this->_db_user . "<br />\n";
        if (is_array($this->_cur_db_name)) {
            $errstr .= "DB name: " . $this->_cur_db_name . "<br />\n";
        }
        if ($this->_reset_error_handler) {
            $old_error_handler = set_error_handler(array($this, "_dbErrorHandler"));
        }
        if ($this->_error_log) {
            error_log("\n[".date("Y-m-d H:i:s")."]\n".strip_tags($errstr), 3, $this->_error_log_file);
        }
        if (!array_key_exists("SERVER_NAME", $_SERVER)) {
            $errstr = strip_tags($errstr);
        }
        trigger_error($errstr, $errtype);
        if ($this->_reset_error_handler) {
            restore_error_handler();
        }
    }
    ///////////////////////////////////////////////////////////////////////////////
    function _is_update_query($query)
    {
        $updates = 'INSERT |UPDATE |DELETE |' .
                   'REPLACE |CREATE |DROP |' .
                   'SET |BEGIN|COMMIT|ROLLBACK|START|END' .
                   'ALTER |GRANT |REVOKE |'.'LOCK |UNLOCK ';
        if (preg_match('/^\s*"?('.$updates.')/i', $query)) {
            return true;
        }
        return false;
    }
/**#@-*/

/**#@+
 * @access public
 */
    /**
     * Конструктор.
     * Не требует вызова на прямую (используйте {@link get_instance()}).
     */
    function MySQL($host, $port, $user, $passwd, $db_names, $charset = '')
    {
        $this->_db_host = $host;
        $this->_db_port = $port;
        $this->_db_user = $user;
        $this->_db_password = $passwd;
        $this->_db_names = $db_names;
        $this->_charset = $charset;
    }
 
    /**
     * Возвращает ссылку на объект класса (синглетон).
     * Статический метод класса.
     *  
     * Если объект не создан, создает его.
     * Для каждого подключения нужно создавать свой объект.
     * После вызова метода кроме создания самого объекта происходит подключение к СУБД
     * и выбор БД по-умолчанию для данного подключения (см. {@link config()}).
     * Удобно использовать внутри других классов и функций, не используя глобальной переменной
     * для хранения объекта:
     * 
     * <code>
     * function some_func()
     * {
     *     $db =& MySQL::get_instance("portal_r");
     * }
     * </code>
     * 
     * Объект создается один раз при первом вызове метода, и в дальнейшем, обращение происходит
     * к одному и тому же объекту в памяти без использования глобальной переменной.
     * 
     * @static
     * @see config()
     * 
     * @param array $connect_name имя подключения, ранее определенное с помощью {@link config()}
     * @return object ссылка объект класса
     */
    static function &get_instance($connect_name = null)
    {
        static $instance;
        if (!isset($connect_name) || empty($connect_name)) {
           $connect_name = "DEFAULT_DB";
        }

        if (!isset($instance[$connect_name])) {

            $connect_data = mysql::config($connect_name, $GLOBALS['_DB_CONFIG']);
            if ($connect_data) {
                $instance[$connect_name] = new MySQL(
                    $connect_data["host"]
                    , isset($connect_data["port"]) && !empty($connect_data["port"])  ? $connect_data["port"] : 3306
                    , $connect_data["user"]
                    , $connect_data["passwd"]
                    , $connect_data["db_names"]
                    , $connect_data["charset"]
                );
                $instance[$connect_name]->connect();
                $db_keys = array_keys($connect_data["db_names"]);
                $instance[$connect_name]->select_db($db_keys[0]);
                $charset = $connect_data["charset"];
                if (!empty($charset))
                	if (!function_exists('mysql_set_charset') || !mysql_set_charset($charset))
						$instance[$connect_name]->query("SET NAMES $charset");
            } else {
                return false;
            }
        }
        return $instance[$connect_name];
    }

    /**
     * Конфигурирует класс.
     * Статический метод класса.
     *  
     * Устанавливает связъ между именем соединения и данными соединения.
     * При задании только $connect_name возвращает данные соединения.
     * 
     * @static
     * @param string $connect_name имя соединения
     * @param array  $connect_data массив вида
     * <code>
     *     $connect_data = array(
     *         "host"     => хост сервера,
     *         "user"     => имя пользователя СУБД,
     *         "passwd"   => пароль пользователя,
     *         "db_names" => array(
     *             // Имена БД, которые будут использоваться
     *             // в этом подключении
     *             "main" => "portal_person"
     *         )
     *         "charset"  => кодировка для работы
     *     );
     * </code>
     * Первая БД в списке "db_names" выбирается для использования автоматически
     * после вызова {@link get_instnce()}. Ключ в массиве "db_names" - упрощенное имя БД,
     * значение - физическое имя БД. Реальные (физические) имена БД могут быть неудобным для использования,
     * когда работа постоянно ведется с несколькими БД. Вместо физических имен, все методы класса могут использовать
     * имена БД, назначенные этим методом.
     * @return array
     */
    static function &config($connect_name, $connect_data = null)
    {
        static $config = array();
        if (!isset($config[$connect_name]) && is_array($connect_data)) {
            $config[$connect_name] = $connect_data;
        }
        return $config[$connect_name];
    }

    /**
     * Устанавливает или сбрасывает внутренний обработчик ошибок.
     * 
     * @param bool $enable true - установить, false - сбросить
     */
    function reset_error_handler($enable = true)
    {
        $this->_reset_error_handler = $enable;
    }

    /**
     * Возвращает последний посланный запрос к БД.
     * 
     * @return string запрос в том виде, в котором он ушел на сервер
     */
    function get_last_query()
    {
        return $this->_last_query;
    }

    /**
     * Возвращает последнюю ошибку при выполнении запроса.
     * 
     * @return string сообщение об ошибке
     */
    function get_last_error()
    {
        return $this->_last_error;
    }

    /**
     * Указывает методам, принимающим в качестве аргументов запрос с параметрами
     * ({@link query()}, {@link insert()}, {@link update()} и др.), экранированы ли значения параметров во внейшней среде или нет
     * (например, в случае включенного флага конфигурации magic_quotes_gpc, когда праметрами служат данные,
     * пришедшие из $_GET, $_POST, $_COOKIE (GPC)).
     * 
     * Значения параметров будут экранироваться в любом случае, не зависимо от того, с каким значением аргумента вызывался этот метод
     * и в каком положении находится переключатель magic_quotes_gpc. Данный метод лишь сообщяет вышеупомянутым методам,
     * как правильно воспринять входные параметры запроса, чтобы избежать "двойных слешей" (двойного экранирования).
     * 
     * В случае, когда данный метод вызывается с аргументом $flag, равным true, заставляющим "думать" методы составления запроса,
     * что параметры уже экранированы, при приеме параметров сначала для каждого из них выполнятся функция stripslashes(),
     * а потом снова происходит экранирование. Совсем не "трогать" параметры нельзя, так как может возникнуть ситуация,
     * когда в запрос попадут неэкранированные значения параметров в случае неверного вызова метода
     * (параметры в действительности не экранированы, а этот метод сообщил об обратном). В этой ситуации могут пострадать лишь значения
     * параметров (удалятся неэкранирующие слеши), но экранирование в любом случае произойдет.  
     * 
     * По умолчанию, все методы, принимающие параметры запросов, "думают", что значения параметров не экранированы.
     * 
     * @param bool $flag true - параметры уже экранированы
     */
    function params_quoted($flag)
    {
        $this->_params_already_quoted = $flag;
    }

    /**
     * Подключение к серверу.
     * 
     * @return bool true - все прошло успешно, false - ошибка 
     */
    function connect()
    {
        ///////////////////////////////////////////////////////////////////////////////
        // Logging
        ///////////////////////////////////////////////////////////////////////////////
        if ($this->_query_log && $this->_query_log_file) {
            $this->_log_connect($this->_db_host, $this->_db_user);
        }
        ///////////////////////////////////////////////////////////////////////////////

        if (!$this->_db_link) {
            $link = @mysql_connect($this->_db_host . ":" . $this->_db_port, $this->_db_user, $this->_db_password);
            if ($link) {
                $this->_db_link = $link;
            } else {
                $this->_error("Unable to connect to MySQL server: ".mysql_error(), E_USER_ERROR);
                return false;
            }
        }
        return true; 
    }

    /**
     * Выбирает текущую БД.
     * 
     * @param string $db_name упрощенное имя БД, заданное ранее с помощью {@link config()}
     * @return string упрощенное имя предыдущей БД (той, которая была текущей до вызова метода)
     */
    function select_db($db_name)
    {
        $real_db_name = $this->_get_real_db_name($db_name);
        
        if ($real_db_name == $this->get_current_db()) {
            return $db_name;
        }

        ///////////////////////////////////////////////////////////////////////////////
        // Logging
        ///////////////////////////////////////////////////////////////////////////////
        if ($this->_query_log && $this->_query_log_file) {
            $this->_log_selecting_db($real_db_name);
        }
        ///////////////////////////////////////////////////////////////////////////////

        $res = @$this->__select_db($real_db_name, $this->_db_link);
        if (!$res) {
            $this->_error("Error on selecting database \"$real_db_name\": " . $this->__error($this->_db_link));
            return false;
        }
        $old_db_name = $this->_cur_db_name;
        $this->_cur_db_name = $real_db_name;
        return $old_db_name ? $old_db_name : true;
    }

    /**
     * Закрытие соединения с серевером БД.
     * @see connect()
     */
    function close()
    {
        $this->__close($this->_db_link);
        $this->_db_link = null;
    }

    /**
     * Выполнение запроса к БД.
     * 
     * Формирует запрос к БД на основе строки запроса с параметрами (placeholders) и самих параметров.
     * (см. {@link params_quoted()}). Параметр $params может быть как ассоциативным массиом (параметр => значение),
     * так и просто значением, в случае, когда в запрос требуется вставить только один параметр.
     * Имена параметров (placeholders) в строке запроса обрабляются с обоих сторон знаком "%".
     * Примеры использования:
     * 
     * <code>
     *     ...
     *     $params = array(
     *         "foo1" => "'qwerty'",
     *         "foo2" => 3
     *     );
     *     $db->query("SELECT * FROM foo WHERE foo1 = %foo1% AND foo2 = %foo2%", $params);
     *     // результирующий запрос:
     *     // SELECT * FROM foo WHERE foo1 = '\'qwerty\'' AND foo2 = '3'
     *     ...
     *     $foo_id = 12;
     *     $db->query("SELECT field FROM foo WHERE foo_id = %id%", $foo_id);
     *     // результирующий запрос:
     *     // SELECT field FROM foo WHERE foo_id = '12'
     *     ...
     * </code>
     * 
     * В строке запроса допустимо явное указание принадлежности таблицы к той или иной БД.
     * Имеется ввиду, что имя БД задается как упрощенное имя, установленное ранее с помощью {@link config()}
     * (указание физического имени БД как "имя_бд.имя_таблицы" никто не отменял, но использовать физическое
     * имя не рекомендуется, так как оно может быть изменено в силу ряда причин). Выбрать текущую БД для запроса
     * можно также и с помощью параметра $db_name. Разница состоит лишь в том, что в последнем случае перед
     * и после выполнения запроса вызывается функция {@link select_db()} для назначения текущей БД, а также
     * для возврата к БД, которая была текущей до вызова этого метода. Второй способ работает медленнее, но в некоторых случаях
     * удобнее (например, когда в запросе участвуют несколько таблиц из одной БД).
     * Пример:
     * 
     * <code>
     *     ...
     *     $db->add_db_name("main", "db00645");
     *     $db->add_db_name("log", "db00645_log");
     *     $db->select_db("main");
     *     $db->query("SELECT * FROM #main#.foo");
     *     // результирующий запрос:
     *     // SELECT * FROM db00645.foo
     *     ...
     *     $db->query("SELECT * FROM #log#.foo");
     *     // результирующий запрос:
     *     // SELECT * FROM db00645_log.foo
     *     // равносильный вызов метода:
     *     // $db->query("SELECT * FROM foo", null, null, "log");
     *     ...
     * </code>
     * 
     * Преимущества передачи параметров как ассоциативных массивов перед простым перечислением значений в аргументах функции
     * (что часто встречается в других классах работы с БД):
     *  - не имеет значения порядок, в котором указываются параметры как в строке запроса так и в массиве значений параметров;
     *  - вызов метода выглядит компактнее;
     *  - довольно часто при программировании мы имеем дело с готовыми (не создаваемыми специально для запроса)
     *    ассоциативными массивами данных (например, $_POST и $_GET), элементы которых могут смело выступать
     *    в качестве параметров запроса (при совпадении имен ключей с именами параметров)
     * 
     * @param string $query   запрос с параметрами (placeholders)
     * @param mixed  $params  параметры запроса (см. {@link params_quoted()})
     * @param string $db_name имя БД для запроса (см. {@link config()}); по-умолчанию - текущая
     * 
     * @return resource результат запроса
     */
    function &query($query, $params = null, $db_name = null)
    {
        ///////////////////////////////////////////////////////////////////////////////
        // Logging connection
        ///////////////////////////////////////////////////////////////////////////////
        if ($this->_query_log && $this->_query_log_file) {
            $this->_log_connect($this->_db_host, $this->_db_user);
        }
        ///////////////////////////////////////////////////////////////////////////////

        $query = $this->get_query($query, $params, $db_name);

        if ($db_name) {
            $this->select_db($db_name);
        }

        $this->_last_query = $query;

        // Mark start time
        if ($this->_query_log && $this->_query_log_verbose) {
            $start_run_time = explode(" ", microtime(1));
            $start_run_time = $start_run_time[0] + $start_run_time[1];
        }

        $result = $this->__query($query, $this->_db_link);

        // Mark end time
        if ($this->_query_log && $this->_query_log_verbose) {
            $end_run_time = explode(" ", microtime(1));
            $end_run_time = $end_run_time[0] + $end_run_time[1];
        }

        if ($result === false) {
            $error = trim($this->__error($this->_db_link));
            $this->_last_error = $error;
            $m = array();
            preg_match("/(?:\'|\")(.*)(?:\'|\")/U", $error, $m);
            if ($m[1]) {
                $error = preg_replace("/(".preg_quote($m[1]).")/U", "<font color='red'><b>\\1</b></font>", str_replace("\n", "", $error));
                $query = preg_replace("/(".preg_quote($m[1]).")/U", "<b>\\1</b>", $query);
            }
            $error_text = "SQL error: $error in query<br />\n";
            $error_text .= "<font color=\"red\">".nl2br($query)."</font>";
            $this->_error($error_text, E_USER_WARNING);
        } else {
            ////////////////////////////////////////////////////
            // Logging
            ////////////////////////////////////////////////////
            if ($this->_query_log && $this->_query_log_file) {
                $this->_log_query($query, "normal", $start_run_time, $end_run_time);
            }
            ////////////////////////////////////////////////////
        }
        
        if ($db_name) {
            $this->select_db($this->_cur_db_name);
        }
        return $result;
    }

    /**
     * Вставка одной строки в таблицу.
     * Пример:
     * 
     * <code>
     *     ...
     *     $data = array(
     *         "foo1" => "text",
     *         "foo2" => 5,
     *     );
     *     $db->insert("foo", $data);
     *     // результирующий запрос:
     *     // INSERT INTO foo (foo1, foo2) VALUES('text', '5')
     *     ...
     * </code>
     * 
     * С помощью этого метода удобно записывать в таблицу данные, пришедшие с формы, если имена полей формы совпадают
     * с именами полей таблицы:
     * 
     * <code>
     *     $db->insert("foo", $_POST);
     * </code>
     * 
     * @param string $table   имя таблицы
     * @param array  $params  ассоциативный массив ("поле" => "значение") данных для вставки
     *                        (подробнее о параметрах см. {@link query()}, {@link params_quoted()}) 
     * @param string $db_name имя БД для запроса (см. {@link config()}); по-умолчанию - текущая
     * 
     * @return bool false, если ошибка, true - при успешном выполнении
     */
    function insert($table, $params, $db_name = null)
    {
        $insert_fields = "";
        $insert_params = "";
        $values = array();
        foreach($params as $key => $value) {
            $insert_fields .= $this->_field_quote . $key.$this->_field_quote . ", ";
            $insert_params .= $this->_param_char  . $key.$this->_param_char  . ", ";
            $values[$key] = $value;
        }
        $insert_fields = substr($insert_fields, 0, strlen($insert_fields) - 2);
        $insert_params = substr($insert_params, 0, strlen($insert_params) - 2);

        $res = $this->query("INSERT INTO $table (\n\t$insert_fields\n) VALUES (\n\t$insert_params\n)", $values, $db_name);
        if ($res) {
            return $this->affected_rows();
        } else {
            $this->_db_error = true;
            return false;
        }
    }

    /**
     * Обновление строк в таблице.
     * 
     * Метод составляет запрос из переданных ему параметров и посылает его в БД на выполнение.
     * 
     * Пример 1.
     * <code>
     *     ...
     *     $data = array(
     *         "foo1" => "text",
     *         "foo2" => 5,
     *     );
     *     $where = array(
     *         "foo1_id" => 1,
     *         "foo2_id" => 5,
     *     );
     *     // $where - массив
     *     $db->update("foo", $data, $where);
     *     // результирующий запрос:
     *     // UPDATE foo SET foo1 = 'text', foo2 = '5' WHERE foo1_id = '1' AND foo2_id => '5'
     *     ...
     * </code>
     *
     * Пример 2. 
     * <code>
     *     ...
     *     $data = array(
     *         "foo1" => "text",
     *         "foo2" => 5,
     *     );
     *     $where_params = array(
     *         "foo1" => "text2",
     *         "foo2" => 1
     *     );
     *     // $where - строка с несколькими параметрами
     *     $db->update("foo", $data, "foo1 = %foo1% OR foo2 = %foo2%", $where_params);
     *     // результирующий запрос:
     *     // UPDATE foo SET foo1 = 'text', foo2 = '5' WHERE foo1 = 'text2' OR foo2 = '1' 
     *     ...
     * </code>
     *
     * Пример 3. 
     * <code>
     *     ...
     *     $data = array(
     *         "foo1" => "text",
     *         "foo2" => 5,
     *     );
     *     $where_param = 7;
     *     // $where, строка с одним параметром
     *     $db->update("foo", $data, "foo_id = %id%", $where_param);
     *     // результирующий запрос:
     *     // UPDATE foo SET foo1 = 'text', foo2 = '5' WHERE foo_id = '7' 
     *     ...
     * </code>
     * 
     * @param string $table        имя таблицы
     * @param array  $params       ассоциативный массив ("поле" => "значение") данных обновления
     *                             (подробнее о параметрах см. {@link query()}, {@link params_quoted()}) 
     * @param mixed  $where        условие WHERE для UPDATE
     *                             (может быть как ассоциативным массивом для склейки по AND, так и строкой запроса с параметрами,
     *                             значения которых берутся из $where_params)  
     * @param mixed  $where_params ассоциативный массив параметров или одно значение параметра выражения $where;
     *                             имеет смысл указывать только, когда $where - строка с параметрами 
     * @param string $db_name      имя БД для запроса (см. {@link config()}); по-умолчанию - текущая
     * 
     * @return bool|int false, если ошибка; число затронутых строк при успешном выполнении (не путать 0 и false)
     */
    function update($table, $params, $where = null, $where_params = array(), $db_name = null)
    {
        $update_set = "";
        $update_values = array();
        foreach ($params as $key => $value) {
            $update_set .= $this->_field_quote . $key . $this->_field_quote .
                           " = " .
                           $this->_param_char . $key . $this->_param_char .
                           ", ";
            $update_values[$key] = $value;
        }
        $update_set = substr($update_set, 0, strlen($update_set) - 2);

        if (is_array($where)) {
            $where_str = "";
            foreach ($where as $key => $value) {
                $where_str .= $this->_field_quote . $key . $this->_field_quote . " " .
                              ($value ? "=" : "IS") . " " .
                              $this->_param_char . $key . $this->_param_char .
                              " AND ";
            }
            $where_str = substr($where_str, 0, strlen($where_str) - 5);
        } elseif (!is_array($where_params)) {
            $m = array();
            if (preg_match_all("/" . $this->_param_char . "(.+)" . $this->_param_char . "/U", $where_params, $m)) {
                $placeholders = array_unique($m[1]);
                $where_params = array($placeholders[0] => $where_params);
            }
        }

        $part1 = $this->get_query(
            ($where
                ? (is_array($where)
                       ? "\nWHERE \n\t$where_str"
                       : "\nWHERE \n\t$where"
                  )
                : ""
            ),
            is_array($where)
                ? $where
                : $where_params
         );
        $part2 = $this->get_query("UPDATE $table SET \n\t$update_set ", $update_values);
        $res = $this->query($part2 . $part1, null, $db_name);
        if ($res) {
            return $this->affected_rows();
        } else {
            $this->_db_error = true;
            return false;
        }
    }

    /**
     * Удаление строк таблицы.
     * 
     * @param string $table        имя таблицы
     * @param mixed  $where        условие WHERE для DELETE
     *                             (может быть как ассоциативным массивом для склейки по AND, так и строкой запроса с параметрами,
     *                             значения которых берутся из $where_params)  
     * @param mixed  $where_params ассоциативный массив параметров или одно значение параметра выражения $where;
     *                             имеет смысл указывать только, когда $where - строка с параметрами 
     * @param string $db_name      имя БД для запроса (см. {@link config()}); по-умолчанию - текущая
     * 
     * @return bool|int false, если ошибка; число удаленных строк при успешном выполнении (не путать 0 и false)
     */
    function delete($table, $where = "", $where_params = array(), $db_name = null)
    {
        if (is_array($where)) {
            $where_str = "";
            foreach($where as $key => $value) {
                $where_str .= $this->_field_quote . $key . $this->_field_quote . " ".
                              ($value ? "=" : "IS") . " " .
                              $this->_param_char . $key . $this->_param_char .
                              " AND ";
            }
            $where_str = substr($where_str, 0, strlen($where_str) - 5);
        } elseif (!is_array($where_params)) {
            $m = array();
            if (preg_match_all("/" . $this->_param_char . "(.+)" . $this->_param_char . "/U", $where_params, $m)) {
                $placeholders = array_unique($m[1]);
                $where_params = array($placeholders[0] => $where_params);
            }
        }

        $res = $this->query(
            "DELETE FROM $table " .
            ($where
                ? (is_array($where)
                       ? "\nWHERE \n\t$where_str"
                       : "\nWHERE \n\t$where"
                  )
                : ""
            ),
            is_array($where)
                ? $where
                : $where_params
            , $db_name
        );
        if ($res) {
            return $this->affected_rows();
        } else {
            $this->_db_error = true;
            return false;
        }
    }

    /**
     * Возвращает первую строку запроса как ассоциативный массив.
     * 
     * Пример.
     * 
     * <code>
     *     // Таблица foo:
     *     // +-------------+-----------+
     *     // | id |  nick  | full_name |
     *     // +-------------+-----------+
     *     // | 10 | john   | John Doe  |
     *     // | 11 | cat    | Katrina   |
     *     // +-------------+-----------+
     *     
     *     $data = $db->get_first("SELECT id, nick, full_name FROM foo WHERE id = 10");
     *     
     *     // $data = array(
     *     //     "id"        => 10,    
     *     //     "nick"      => "john",    
     *     //     "full_name" => "John Doe"
     *     // )
     *     ...
     * </code>
     *
     * @see query()
     * 
     * @param string $query   запрос с параметрами (placeholders)
     * @param array  $params  параметры запроса (см. {@link query()}, {@link params_quoted()})
     * @param string $db_name имя БД для запроса (см. {@link config()}); по-умолчанию - текущая
     * 
     * @return array строка запроса в виде ассоциативного массива ("атрибут" => "значение")
     */
    function &get_first($query, $params = null, $db_name = null)
    {
        $res =& $this->query($query, $params, $db_name);
        if (!$res) {
            $this->_db_error = true;
        } else {
            $row = $this->__fetch_assoc($res);
            $this->__free_result($res);
        }
        return $row;
    }

    /**
     * Включает/выключает ведение логов ошибок выполнения запросов.
     * 
     * @param bool   $enable   включить/выключить ведение логов ошибок (true/false)
     * @param string $log_file имя файла лога (задав при первом вызове, больше указывать не обязательно) 
     */
    function enable_error_log($enable, $log_file = null)
    {
        $this->_error_log_file = $log_file;
        $this->_error_log = $enable;
    }
    
    /**
     * Определяет файл для ведения логов запросов.
     * 
     * @param string $log_file имя файла лога 
     */
    function set_query_log_file($log_file)
    {
        $this->_query_log_file = $log_file;
    }
    
    /**
     * Включает ведение логов запросов.
     * 
     * @param bool $only_updates true - вести лог только изменений БД
     * @param bool $verbose      true - выводить в лог дополнительную информацию
     *                           (имя скрипта, из которого был вызван запрос, номер строки в файле скрипта и т.п.)
     */
    function enable_query_log($only_updates = false, $verbose = false)
    {
        $this->_query_log = true;
        $this->_query_log_verbose = $verbose;
        $this->_query_log_only_updates = $only_updates;
    }
    
    /**
     * Выключает ведение логов запросов.
     */
    function disable_query_log()
    {
        $this->_query_log = false;
        $this->_query_log_verbose = false;
    }
    
    /**
     * Устанавливает границу начала отлова ошибок.
     * 
     * Удобно использовать при выполнении группы запросов для отлова ошибок их выполнения.
     * Чтобы не проверять каждый запрос на корректное выполнение, можно установить в начале группы
     * границу начала отлова ошибок, а затем в конце группы проверить методом {@link error_occured()},
     * произошла ли ошибка при выполнении какого-нибудь из запросов группы или нет.
     * Пример:
     * 
     * <code>
     *     $db->catch_error();
     * 
     *     $db->update("foo", $data);
     *     $db->insert("foo2", $data2);
     *     $db->select("SELECT * FROM foo");
     * 
     *     if ($db->error_occured()) {
     *         echo "Ошибка!";
     *     }
     * </code> 
     * 
     * @see error_occured()
     */
    function catch_error()
    {
        $this->_db_error = false;
    }

    /**
     * Возвращает true, если произошла ошибка в группе запросов (после вызова {@link catch_error()}).
     * 
     * @see catch_error()
     */
    function error_occured()
    {
        return $this->_db_error;
    }
    
    /**
     * То же, что и {@link fetch_all() fetch_all($res DB_FETCH_ALL_VECTOR)}.
     * Отличие в том, что первым параметром может выступать как ресурс (результат запроса),
     * так и строка самого запроса.
     * 
     * @param string|resource $query   запрос с параметрами (placeholders) или результат запроса
     * @param array           $params  параметры запроса (см. {@link query()}, {@link params_quoted()})
     * @param string          $db_name имя БД для запроса (см. {@link config()}); по-умолчанию - текущая
     */
    function &get_vector($query, $params = null, $db_name = null)
    {
        if (is_resource($query)) {
            $res =& $query;
        } else {
            $res =& $this->query($query, $params, $db_name);
        }
        if (!$res) {
            $this->_db_error = true;
            return false;
        }
        $ret =& $this->fetch_all($res, DB_FETCH_ALL_VECTOR);
        if (!is_resource($query)) {
            $this->__free_result($res);
        }
        return $ret;
    }

    /**
     * То же, что и {@link fetch_all() fetch_all($res DB_FETCH_ALL_INDEX_KEYS)}.
     * Отличие в том, что первым параметром может выступать как ресурс (результат запроса),
     * так и строка самого запроса.
     * 
     * @param string|resource $query   запрос с параметрами (placeholders) или результат запроса
     * @param array           $params  параметры запроса (см. {@link query()}, {@link params_quoted()})
     * @param string          $db_name имя БД для запроса (см. {@link config()}); по-умолчанию - текущая
     */
    function &get_list($query, $params = null, $db_name = null)
    {
        if (is_resource($query)) {
            $res =& $query;
        } else {
            $res =& $this->query($query, $params, $db_name);
        }
        if (!$res) {
            $this->_db_error = true;
            return false;
        }
        $ret =& $this->fetch_all($res, DB_FETCH_ALL_INDEX_KEYS);
        if (!is_resource($query)) {
            $this->__free_result($res);
        }
        return $ret;
    }

    /**
     * То же, что и {@link fetch_all() fetch_all($res DB_FETCH_ALL_VALUE_KEYS)}.
     * Отличие в том, что первым параметром может выступать как ресурс (результат запроса),
     * так и строка самого запроса.
     * 
     * @param string|resource $query     запрос с параметрами (placeholders) или результат запроса
     * @param array           $params    параметры запроса (см. {@link query()}, {@link params_quoted()})
     * @param string          $key_field поле, значения которого участвуют в образовании ключей массива;
     *     если не указан, то берутся значения первого поля
     * @param string          $db_name   имя БД для запроса (см. {@link config()}); по-умолчанию - текущая
     */
    function &get_key_list($query, $params = null, $key_field = null, $db_name = null)
    {
        if (is_resource($query)) {
            $res =& $query;
        } else {
            $res =& $this->query($query, $params, $db_name);
        }
        if (!$res) {
            $this->_db_error = true;
            return false;
        }
        $ret =& $this->fetch_all($res, DB_FETCH_ALL_VALUE_KEYS, $key_field);
        if (!is_resource($query)) {
            $this->__free_result($res);
        }
        return $ret;
    }

    /**
     * Вытаскивает все строки из результата SELECT запроса в массив.
     * 
     * Есть несколько типов извлечения строк в массив.
     * 
     * 1. Рузультат: массив, каждая строка которого - ассоциативный массив вида "поле" => "значение".
     *
     * <code>
     *     // Таблица foo:
     *     // +-------------+-----------+
     *     // | id |  nick  | full_name |
     *     // +-------------+-----------+
     *     // | 10 | john   | John Doe  |
     *     // | 11 | cat    | Katrina   |
     *     // +-------------+-----------+
     * 
     *     $res = $db->query("SELECT id, nick, full_name FROM foo");
     *     $data = $db->fetch_all($res, DB_FETCH_ALL_INDEX_KEYS);
     * 
     *     // $data = array(
     *     //     [0] => array(
     *     //         "id"        => 10,    
     *     //         "nick"      => "john",    
     *     //         "full_name" => "John Doe"
     *     //     ),
     *     //     [1] => array(
     *     //         "id"        => 11,    
     *     //         "nick"      => "cat",    
     *     //         "full_name" => "Katrina"
     *     //     )
     *     // )
     *     ...
     * </code>
     *  
     * 2. Результат: массив, каждая строка которого - ассоциативный массив вида "поле" => "значение".
     *    Значения поля результата, имя которого указано в $key_field, участвуют в образовании ключей массива строк
     *    (в самой строке значение ключевого поля уже не присутствует). Если $key_field не указан, в
     *    качестве ключей массива выступают значения первого столбца результата.
     *
     * <code>
     *     $res = $db->query("SELECT id, nick, full_name FROM foo");
     *     $data = $db->fetch_all($res, DB_FETCH_ALL_VALUE_KEYS);
     * 
     *     // $data = array(
     *     //     [10] => array(
     *     //         "nick"      => "john",    
     *     //         "full_name" => "John Doe"
     *     //     ),
     *     //     [11] => array(
     *     //         "nick"      => "cat",    
     *     //         "full_name" => "Katrina"
     *     //     )
     *     // ) 
     *     ...
     * </code>
     *  
     * 3. Результат: одномерный массив типа "значение 1-го поля" => "значение 2-го поля".
     *    Если результат содержит только один столбец, ключи задаются автоматически.
     *
     * <code>
     *     $res = $db->query("SELECT id, nick FROM foo");
     *     $data = $db->fetch_all($res, DB_FETCH_ALL_VECTOR);
     *     // array(
     *     //     [10] => "john",
     *     //     [11] => "cat"
     *     // )
     * 
     *     // когда только одно поле
     *     $res = $db->query("SELECT nick FROM foo");
     *     $data = $db->fetch_all($res, DB_FETCH_ALL_VECTOR);
     *     // array(
     *     //     [0] => "john",
     *     //     [1] => "cat"
     *     // )
     *     ...
     * </code>
     * 
     * @param resource $res         Результат запроса (возвращается методом {@link query()}).
     * @param int      $result_type Вид полученного в результате массива<br />
     *     Возможные значения $result_type:<br />
     *     DB_FETCH_ALL_INDEX_KEYS   - двумерный массив, каждая строка которого -
     *        ассоциативный массив вида "поле" => "значение";<br />
     *     DB_FETCH_ALL_VALUE_KEYS - такой же, как DB_FETCH_ALL_INDEX_KEYS,
     *        но значения поля результата, указанного в $column, участвуют в образовании ключей массива строк;<br />
     *     DB_FETCH_ALL_VECTOR     - одномерный массив вида "значение_первого_поля" => "значение_второго_поля".
     * @param string $key_field     Поле, значения которого участвуют в образовании ключей, когда
     *     аргумент $result_type принимает значение DB_FETCH_ALL_VALUE_KEYS; если не указан, то берутся
     *     значения первого столбца результата.
     * 
     * @return array рузультат в виде массива
     */
    function &fetch_all(&$res, $result_type = DB_FETCH_ALL_INDEX_KEYS, $key_field = null)
    {
        if ($res) {
            // сбрасываем указатель в результате на начало
            $this->__data_seek($res, 0);    
            if (is_int($result_type)) {
                $data = array();
                $i = 0;
                while ($row = $this->__fetch_assoc($res)) {
                    if ($result_type == DB_FETCH_ALL_INDEX_KEYS) {
                        $data[$i] = $row;
                    }
                    if ($result_type == DB_FETCH_ALL_VALUE_KEYS) {
                        if (!$key_field) {
                            $keys = array_keys($row);
                            $key_field = $keys[0];
                        }
                        $data[$row[$key_field]] = $row;
                        unset($data[$row[$key_field]][$key_field]);
                    }
                    if ($result_type == DB_FETCH_ALL_VECTOR) {
                        $keys = array_keys($row);
                        if ($keys[1]) {
                            $data[$row[$keys[0]]] = $row[$keys[1]];
                        } else {
                            $data[] = $row[$keys[0]];
                        }
                    }
                    $i++;
                }
                return $data;
            } else {
                $this->_error("Unknown result type: ".$result_type);
                return false;
            }
        } else {
            $this->_error("First argument in fetch_all() is not a valid query result!");
            return false;
        }
    }
    
    /**
     * Возвращает сгенерированный запрос без его выполнения.
     * 
     * @param string $query  запрос с параметрами (placeholders)
     * @param array  $params параметры запроса (см. {@link query()}, {@link params_quoted()})
     * 
     * @return string рузультирующий запрос
     */
    function get_query($query, $params)
    {

        $m = array();
        if (preg_match_all("/" . $this->_param_char . "(.+)" . $this->_param_char . "/U", $query, $m)) {
            $placeholders = array_unique($m[1]);
            if(!is_array($params)) {
                if(strpos($params, "%") === false) {
                    $params = array($placeholders[0] => $params);
                } else {
                    $params = array();
                }
            }
            foreach($placeholders as $value) {
                if(isset($params[$value])) {
                    $key = $value;
                    $value = $params[$key];
                    if ($this->_params_already_quoted) {
                        $value = stripslashes($value);
                    }
                    $value = $this->__escape_string($value."");
                    $value = str_replace("-", "\-", $value);
                    $value  = "'".$value."'";
                    $query = str_replace($this->_param_char . $key . $this->_param_char, $value, $query);
                }
            }
        }

        if (is_array($this->_db_names)) {
            foreach ($this->_db_names as $key => $value) {
                $query = str_replace($this->_db_char . $key . $this->_db_char, $value, $query);
            }
        }
        return $query;
    }
    
    /**
     * Высвобождает памать, отведенную под результат запроса.
     * 
     * @param resource $res ссылка на рузультат запроса
     */
    function free_result(&$res)
    {
        if ($res) {
            $this->__free_result($res);
        } else {
            $this->_error("First argument in free_result() is not a valid query result!");
            return false;
        }
    }
    
    /**
     * Возвращает следующую строку результата запроса как одномерный массив.
     * 
     * @param resource $res Рузультат запроса.
     * @param int $fetch_mode вид полученного в результате массива;<br />
     *     DB_FETCH_ASSOC - ассоциативный массив,<br />
     *     DB_FETCH_NUM   - массив с числовыми ключами.
     */
    function &fetch_row(&$res, $fetch_mode = DB_FETCH_ASSOC)
    {
        if ($res) {
            switch ($fetch_mode) {
                case DB_FETCH_ASSOC:
                    $row = $this->__fetch_assoc($res);
                    break;
                case DB_FETCH_NUM:
                    $row = $this->__fetch_num($res);
                    break;
            }
            return $row;
        } else {
            $this->_error("First argument in fetch_row() is not a valid query result!");
            return false;
        }
    }
    
    /**
     * Возвращает значение первого столбца первой строки запроса.
     *
     * Пример.
     * 
     * <code>
     *     // Таблица foo:
     *     // +-------------+-----------+
     *     // | id |  nick  | full_name |
     *     // +-------------+-----------+
     *     // | 10 | john   | John Doe  |
     *     // | 11 | cat    | Katrina   |
     *     // +-------------+-----------+
     *
     *     $num = $db->get_one("SELECT COUNT(*) FROM foo");
     *     // $num = 2;
     *     ...
     * </code>
     *
     * @see query()
     * 
     * @param string|resource $query   запрос с параметрами (placeholders) или результат запроса
     * @param array           $params  параметры запроса (см. {@link query()}, {@link params_quoted()})
     * @param string          $db_name имя БД для запроса (см. {@link config()}); по-умолчанию - текущая
     */
    function get_one($query, $params = null, $db_name = null)
    {
        if (is_resource($query)) {
            $res =& $query;
        } else {
            $res =& $this->query($query, $params, $db_name);
        }
        if (!$res) {
            $this->_db_error = true;
            return false;
        } else {
            $row = $this->fetch_row($res, DB_FETCH_NUM);
            if (!is_resource($query)) {
                $this->__free_result($res);
            }
            return $row[0];
        }
    }

    /**
     * Устанавливает внутренний указатель рузультата запроса на первую строку.
     * 
     * @param resource $res ссылка на результат запроса
     */
    function result_reset(&$res)
    {
        if ($res) {
            $this->__data_seek($res, 0);    
        } else {
            $this->_error("First argument in result_reset() is not a valid query result!");
            return false;
        }
    }
    
    /**
     * Возвращает количество строк результата запроса.
     * 
     * @param resource $res на результат запроса
     * @return int количество строк результата
     */
    function num_rows(&$res)
    {
        return $this->__num_rows($res);
    }
    
    /**
     * Возвращает количество измененных записей последнего запроса UPDATE/DELETE.
     * 
     * @return int количество строк результата
     */
    function affected_rows()
    {
        return $this->__affected_rows();
    }

    /**
     * Экранирует содержимое переменной.
     * 
     * Может пригодится в том случае, когда нужно вставить переменную непосредственно в строку запроса (не прибегая к использованию
     * параметров). $var может быть массивом. В этом случае экранируется все содержимое массива.
     * Пример:
     * 
     * <code>
     *     $var = "qwer'ty";
     *     escape_var($var);
     *     $db->query("SELECT * FROM foo WHERE foo = '$var'");
     *     // SELECT * FROM foo WHERE foo = 'qwer\'ty'; 
     *     ...
     * </code>
     * 
     * Однако не рекомендуется использовать такой подход к построению запросов.
     * Используйте параметризованные запросы (см. {@link query()}).
     * 
     * @see params_quoted()
     * 
     * @param  mixed $var ссылка на переменную, значение которой нужно экранировать
     * @return mixed экранированная переменная
     */
    function &escape_var(&$var)
    {
        if (is_array($var)) {
            foreach ($var as $key => $value) {
                $this->escape_var($var[$key]);
            }
        } else {
            if ($var) {
                $var = str_replace("ё", "е", $var);
                $var = str_replace("Ё", "Е", $var);
            }
            $var = $this->__escape_string($var);
            $var = str_replace("-", "\-", $var);
        }
        return $var;
    }
    
    /**
     * Возвращает список полей таблицы.
     * 
     * @param string $table_name имя таблицы
     * @param string $db_name    имя БД где, находится таблица (см. {@link config()})
     */
    function &table_fields($table_name, $db_name = null)
    {
        return $this->__table_fields($table_name, $db_name = null);
    }
    
    /**
     * Возвращает физическое имя текущей БД.
     */
    function get_current_db()
    {
        return $this->_cur_db_name;
    }
    
    /**
     * Возвращает информацию о соединении.
     * 
     * Возвращает массив вида:
     *     array(
     *         "host"     => хост сервера БД,
     *         "port"     => порт сервера БД,
     *         "login"    => логин пользователя БД,
     *         "passwd"   => пароль пользователя БД,
     *         "link"     => линк подключения к серверу 
     *         "db_names" => список имен БД подключения
     *     )
     * 
     * @return array информация о соединении
     */
    function &get_connection()
    {
        $ret = array(
            "host"     => $this->_db_host,
            "port"     => $this->_db_port,
            "login"    => $this->_db_user,
            "passwd"   => $this->_db_password,
            "link"     => $this->_db_link,
            "db_names" => $this->_db_names,
            "charset"  => $this->_charset
        );
        return $ret; 
    }
    
    /**
     * Возвращает значение автоинкрементного поля таблицы после последней вставки.
     * 
     * @return int идентификатор вставленной строки
     */
    function insert_id()
    {
        //$last_id = $this->get_one("SELECT LAST_INSERT_ID()");
        $last_id = mysql_insert_id($this->_db_link); 
        return $last_id;
    }
/**#@-*/




    /**
     * 
     * импорт файла в текущую базу
     * 
     */
    var $file;

    /*function sdGetSqlLine() {
        $res = "";
        $fl = true;
        while (!feof($this->file) && $fl) {
            $str = ltrim(fgets($this->file, 4096));
    		if (!empty($str) && !preg_match("/^(#|--)/", $str)) {
                //$fl = (strpos($str, ";") === false);
                $fl = preg_match("/;$/six", $str);
                $res .= str_replace("\r\n", " ", $str);
            }
        }
        return $res;
    }*/

    function sdImportFromFile($fname) {
        $this->file = @file($fname);
        if (!$this->file) {
            $this->_error("Can't read mysql dump: <b>".$fname."</b>");
        }
        $total = 0;
        $query = "";
        foreach ($this->file as $line) {
            /*$sql = $this->sdGetSqlLine();
            if ($sql) {
                $this->query($sql);
                $total++;
            }*/
			if (preg_match("/^\s?#/", $line) || !preg_match("/[^\s]/", $line))
				continue;
			else {
				$query .= $line;
				if (preg_match("/;\s?$/", $query)) {
					$this->query($query);
					$total++;
					$query = '';
				}
			}
        }
        //fclose($this->file);
        return $total;
    }

};
?>