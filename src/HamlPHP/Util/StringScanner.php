<?php

require_once 'BaseObject.php';

if(!defined('MATCH_MODE_HEAD_ONLY'))
{
	define('MATCH_MODE_HEAD_ONLY', 'headonly');
	define('MATCH_MODE_ANYWHERE', 'anywhere');
}

/** @class StringScanner
 * @nosubgrouping
 * StringScanner provides for lexical scanning operations on a String. 
 * This class is a port of Ruby's StringScan class. So the documentation was "stolen" from there.
 * Here is an example of its usage:
 * 
 * @code
 *   $s = new StringScanner('This is an example string');
 *   $s->eos;                      # -> false
 * 
 *   echo $s->scan('/\w+/');      # -> "This"
 *   echo $s->scan('/\w+/');      # -> null
 *   echo $s->scan('/\s+/');      # -> " "
 *   echo $s->scan('/\s+/');      # -> null
 *   echo $s->scan('/\w+/');      # -> "is"
 *   $s->eos;                      # -> false
 * 
 *   echo $s->scan('/\s+/');      # -> " "
 *   echo $s->scan('/\w+/');      # -> "an"
 *   echo $s->scan('/\s+/');      # -> " "
 *   echo $s->scan('/\w+/');      # -> "example"
 *   echo $s->scan('/\s+/');      # -> " "
 *   echo $s->scan('/\w+/');      # -> "string"
 *   $s->eos;                      # -> true
 * 
 *   echo $s->scan('/\s+/');      # -> null
 *   echo $s->scan('/\w+/');      # -> null
 * @endcode
 * 
 * <p>
 * Scanning a @mlink{$string} means remembering the position of a scan @mlink{$pointer}, which is just an index.
 * The point of scanning is to move forward a bit at a time, so matches are
 * sought after the scan @mlink{$pointer}; usually immediately after it.
 * </p>
 * 
 * <p>
 * Given the string "test string", here are the pertinent scan @mlink{$pointer} positions:
 * </p>
 * 
 * <pre>
 *     t e s t   s t r i n g
 *   0 1 2 ...             1
 *                         0
 * </pre>
 * 
 * <p>
 * When you {@link scan()} for a pattern (a regular expression), the match must occur at the character after the scan @mlink{$pointer}. 
 * If you use {@link scanUntil()}, then the match can occur anywhere after the scan pointer. In both cases, the scan @mlink{$pointer} moves <em>just beyond</em>
 * the last character of the match, ready to <a href="StringScanner.html#M003869">scan</a> again from the next character
 * onwards. This is demonstrated by the example above.
 * </p>
 * 
 * <h2>Method_Categories Method Categories</h2>
 * 
 * There are other methods besides the plain scanners. You can look ahead in
 * the <a href="StringScanner.html#M003861">string</a> without actually
 * scanning. You can access the most recent match. You can modify the @mlink{$string} being scanned, 
 * {@link reset()} or {@link terminate()} the scanner, find out or
 * change the position of the scan @mlink{$pointer}, {@link skip()} ahead, and so on.
 * 
 * @par Advancing the Scan Pointer
 * 
 * - {@link getch()}
 * - {@link scan()}
 * - {@link scanUntil()}
 * - {@link skip()}
 * - {@link skipUntil()}
 * 
 * @par Looking Ahead
 * 
 * - {@link check()}
 * - {@link checkUntil()}
 * - {@link exist()}
 * - {@link match()}
 * - {@link peek()}
 * 
 * @par Finding Where we Are
 * 
 * - @mlink{$bol} or {@link isBeginningOfLine()}
 * - {@link eos}
 * - @mlink{$rest}
 * - {@link restSize()}
 * - @mlink{$pos} or @mlink{$pointer}
 * 
 * @par Setting Where we Are
 * 
 * - {@link reset()}
 * - {@link terminate()}
 * - @mlink{$pos}
 * 
 * @par Match Data
 * 
 * - {@link matched()}
 * - @mlink{$matchedSize} or {@link getMatchedSize()}
 * - [] (You can use array like syntax to <em>get</em> the last match and it's subgroups
 * - {@link preMatch()}
 * - {@link postMatch()}
 * 
 * @par Miscellaneous
 * 
 * - {@link concat()}
 * - @mlink{$string}
 * - {@link unscan()}
 * 
 * There are aliases to several of the methods.
 * 
 * @todo Implement byte reading
 * 
 * @author Saulo Vallory <email@saulovallory.com>
 *
 * @property-read bool 	  $beginningOfLine Returns true if the scan @mlink{$pointer} is at the beginning of the line
 * @property-read bool    $bol             Returns true if the scan @mlink{$pointer} is at the beginning of a new line. 
 * @property-read bool    $eos             Returns true if the scan @mlink{$pointer} is at the end of the string. 
 * @property-read int     $matchedSize     Returns the size of the most recent match (see @mlink{$matched} or {@link getMatched()}), or null if there was no recent match.
 * @property-read int     $pointer         Returns true if the scan @mlink{$pointer} is at the beginning of a new line.
 * @property-read int     $pos             Returns true if the scan @mlink{$pointer} is at the beginning of a new line.
 * @property-read string  $rest            Returns the "rest" of the string (i.e. everything after the scan @mlink{$pointer}). If there is no more data (@mlink{$eos} = true), it returns "".
 * @property-read string  $restSize        Returns the size of the "rest" of the string.
 * @property string       $string          Returns the string being scanned.
 * @property-read string  $matched         Returns true if the last match was successful.
 */
class StringScanner extends BaseObject implements ArrayAccess
{	 
	/** \name Magic Properties
	 * @{
	 */
	/** @property read_only bool $beginningOfLine
	 * @brief Returns true if the scan @mlink{$pointer} is at the beginning of the line
	 * @memberof StringScanner
	 * @readonly
	 */
		
	/** @property read_only bool $bol
	 * @brief Returns true if the scan @mlink{$pointer} is at the beginning of a new line. 
	 * @memberof StringScanner
	 * @readonly
	 */
		
	/** @property read_only bool $eos
	 * @brief Returns true if the scan @mlink{$pointer} is at the end of the string. 
	 * @memberof StringScanner
	 * @readonly
	 */
		
	/** @property read_only int $matchedSize
	 * @brief Returns the size of the most recent match (see @mlink{$matched} or {@link getMatched()}), or null if there was no recent match.
	 * @memberof StringScanner
	 * @readonly
	 */
	
	/** 
	 * @property read_only int $pointer
	 * @brief Returns the current pointer position.
	 * @memberof StringScanner
	 * @readonly
	 */
		
	/** @property int $pos
	 * @brief Returns true if the scan @mlink{$pointer} is at the beginning of a new line.
	 * @memberof StringScanner
	 */
		
	/** @property read_only string $rest
	 * @brief Returns the "rest" of the string (i.e. everything after the scan @mlink{$pointer}). If there is no more data (@mlink{$eos} = true), it returns "".
	 * @memberof StringScanner
	 * @readonly
	 */
		
	/** @property read_only string $restSize
	 * @brief Returns the size of the "rest" of the string.
	 * @memberof StringScanner
	 * @readonly
	 */
		
	/** @property string $string
	 * @brief Returns the string being scanned.
	 * @memberof StringScanner
	 */
	
 	/** @property read_only string $matched
 	 * @brief Returns the last matched string
	 * @memberof StringScanner
 	 * @readonly
 	 */
	
 	/** @property read_only string $scanned
 	 * @brief Returns the entire string scanned in the last scan*() call
	 * @memberof StringScanner
 	 * @readonly
 	 */
	
 	/** @property read_only string $preMatch
 	 * @brief Returns the substring from the beginning of the scanned string to the start of the part used as match in the previous scanUntil call  
	 * @memberof StringScanner
 	 * @readonly
 	 */

	/** @property read_only string $postMatch
	 * @brief Returns the string yet to be scanned
	 * @memberof StringScanner
	 * @readonly
	 */
	
	/** @} */
	
	private $_string;
	
	private $_curr;
	
	private $_prev;
	
	/**
	 * Last match and it's subgroups
	 * @var array
	 */
	private $_matches;
	
	/**
	 * The beginning pos of the last match
	 * @var int
	 */
	private $_lastMatchBeg;
	
	/**
	 * The ending pos of the last match
	 * @var int
	 */
	private $_lastMatchEnd;
	
	/**
	 * The mode used to match the last regex. Can be MATCH_MODE_HEAD_ONLY or MATCH_MODE_ANYWHERE
	 * @var string
	 */
	private $_lastMatchMode;
	
	private $_rest;

	/**
	 * The length of the {@link string} being parsed
	 * @var int
	 */
	private $_size;
	
	private $_restSize;
	
	private $_encoding = null;
	
	protected $magic_get_methods = array(
		'bol' => 'isBeginningOfLine',
		'beginningOfLine' => 'isBeginningOfLine',
		'pointer' => 'getPos',
		'eos' => 'isEos',
	);
	
	const rQUOTE = '/["\']/';
	
	/**
	 * Creates a new StringScanner object to scan over the given $string.
	 * @param string $str The string to be scanned
	 * @param string $encoding The encoding of the string
	 */
	public function __construct($str, $encoding = null)
	{
	    if($encoding) {
	    	$this->_encoding = $encoding;
	    	mb_internal_encoding($encoding);
	    }
	    else {
	    	$this->_encoding = mb_internal_encoding();
	    }
	    
	    $this->_string = $str;
	    $this->reset();
	}
	
	/**
	 * @internal
	 * @param string $regex The regular expression
	 * @param bool $update_ptr Wheter to update the pointer or not
	 * @param bool $get_str Wheter to return the matched string or the position
	 * @param bool $head_only Match only at the beginning of the string
	 * @return mixed Either the matched string (if $return_string is true) or the end position of the match
	 */
	private function doScan($regex, $update_ptr, $get_str, $head_only)
	{
		if($this->eos)
			return null;
	
		if(!empty($regex))
		{
			$delim = mb_substr($regex, 0, 1);
			$delim_len = mb_strlen($delim);
			$end_pos = mb_strrpos($regex, $delim);

			$options = mb_substr($regex, $end_pos+$delim_len);
			$regex = mb_substr($regex, $delim_len, $end_pos - $delim_len);
			
		    if ($head_only)
		    	$regex = "{$delim}^({$regex}){$delim}{$options}";
		    else
		    	$regex = "{$delim}.*?($regex){$delim}{$options}";
		}
	
	    $ret = preg_match($regex, $this->_rest, $this->_matches);
	    
	    if ($ret === false) 
	    	throw new Exception(preg_last_error());
	    	
	    if ($ret == 0) {
	        // not matched
	        $this->_clear_matched();
	        return null;
	    }
	    
	    $this->_matched($update_ptr, $head_only ? MATCH_MODE_HEAD_ONLY : MATCH_MODE_ANYWHERE);

	    // removes the extra ()s added for processing the regex
	    if($head_only)
	    	array_shift($this->_matches);
	    
	    if ($get_str) {
	        return mb_substr($this->_string, $this->_prev, $this->_lastMatchEnd - $this->_prev);
	    }
	    else {
	        return $this->_lastMatchEnd - $this->_prev;
	    }
	}
	
	/**
	 * @internal
	 * NEEDS to be called whenever the scanner does a new match 
	 * Update optimization vars based on the last match
	 * 
	 * @param bool $update_curr Wheter to update the pointer or not
	 * @param MATCH_MODE $matchMode The mode used to match the regex. Can be MATCH_MODE_HEAD_ONLY or MATCH_MODE_ANYWHERE
	 */
	private function _matched($update_curr, $matchMode)
	{
		$mtch = MATCH_MODE_HEAD_ONLY == $matchMode ? $this->_matches[0] : $this->_matches[1];
		$mtch_pos = mb_strrpos($this->_matches[0], $mtch);
		
	    $this->_prev = $this->_curr;
	    
	    $this->_lastMatchBeg = $this->_curr + (empty($mtch) ? 0 : $mtch_pos !== false ? $mtch_pos : mb_strlen($this->_matches[0]));
	    $this->_lastMatchEnd = $this->_lastMatchBeg + mb_strlen($mtch);
	    $this->_lastMatchMode = $matchMode; 
	    
	    if($update_curr)
	    {
	    	$this->_curr = $this->_lastMatchEnd;
	    	$this->_rest = mb_substr($this->_string, $this->_curr, $this->_size);
	    	$this->_restSize = mb_strlen($this->_rest);
	    }
	}
	
	private function _clear_matched()
	{
		$this->_matches = null;
	    $this->_lastMatchBeg = null;
	    $this->_lastMatchEnd = null;
	}
	
	private function _string_updated($keep_pointer)
	{   
	    if($keep_pointer)
	    {
	    	$this->_size = mb_strlen($this->_string);
	    	$this->_rest = mb_substr($this->_string, $this->_curr, $this->_size);
	    	$this->_restSize = mb_strlen($this->_rest);
	    	
	    	if($this->_curr > $this->_size)
	    		throw new Exception('The operation resulted in an invalid pointer position!');
	    }
	    else
	    {
	    	$this->_prev = null;
	    	$this->_curr = 0;
		    $this->_rest = $this->_string;
	    	$this->_restSize = $this->_size = mb_strlen($this->_string);
		    $this->_matches = $this->_lastMatchBeg = $this->_lastMatchEnd = null;
	    }
	}
	
	/* ArrayAccess methods */
	
	/**
	 * Wether the n-th group was caught or not in the last match.
	 * 0 stands for the whole match.
	 * This method is executed when using isset() or empty().
	 * 
	 * @param int $offset
	 */
	public function offsetExists($offset)
	{
		return isset($this->_matches[$offset]);
	}

	/**
	 * Implements ArrayAccess
	 * Return the n-th subgroup in the most recent match.
	 * @code
	 *   $s = new StringScanner("Fri Dec 12 1975 14:39");
	 *   $s->scan('/(\w+) (\w+) (\d+) /');       # -> "Fri Dec 12 "
	 *   echo $s[0];                             # -> "Fri Dec 12 "
	 *   echo $s[1];                             # -> "Fri"
	 *   echo $s[2];                             # -> "Dec"
	 *   echo $s[3];                             # -> "12"
	 *   echo $s->postMatch();                  # -> "1975 14:39"
	 *   echo $s->preMatch();                   # -> ""
	 * @endcode
	 * @param int $offset
	 */
	public function offsetGet($offset)
	{
		return isset($this->_matches[$offset]) ? $this->_matches[$offset] : null;
	}

	/**
	 * Calling this method will throw an exception.
	 * You can't set the value of the last match.
	 */
	public function offsetSet($offset, $value)
	{
		throw new Exception("You can't set the value of the last match.");
	}

	/**
	 * Calling this method will throw an exception.
	 * You can't unset the value of the last match.
	 */
	public function offsetUnset($offset)
	{
		throw new Exception("You can't unset the value of the last match.");
	}

	/* / ArrayAccess methods */

	/**
	 * Returns true if the scan @mlink{$pointer} is at the beginning of the line.
	 * @magicalias{$bol}
	 * 
	 * @code
	 *   $s = new StringScanner("test\ntest\n");
	 *   $s->bol();           # => true
	 *   $s->scan('/te/');
	 *   $s->bol();           # => false
	 *   $s->scan('/st\n/');
	 *   $s->bol();           # => true
	 *   $s->terminate();
	 *   $s->bol();           # => true
	 * @endcode
	 * 
	 * @return bool
	 */
	public function isBeginningOfLine() {
		return ($this->_curr == 0 || $this->_string[$this->_curr-1] == "\n");
	}
	
	/**
	 * This returns the value that scan would return, without advancing the scan pointer. The match register is affected, though.
	 * @code
	 *   $s = new StringScanner("Fri Dec 12 1975 14:39");
	 *   $s->check('/Fri/');             # -> "Fri"
	 *   $s->pos;                       # -> 0
	 *   $s->matched;                   # -> "Fri"
	 *   $s->check('/12/');             # -> null
	 *   $s->matched;                   # -> null
	 * @endcode  
	 * Mnemonic: it "checks" to see whether a scan will return a value.
	 * 
	 * @param string $pattern
	 */
	public function check($pattern) {
		return $this->doScan($pattern, false, true, true);
	}
	
	/**
	 * This returns the value that {@link scanUntil()} would return, without advancing the scan @mlink{$pointer}. 
	 * The match register is affected, though.
	 * @code
	 *   $s = new StringScanner("Fri Dec 12 1975 14:39");
	 *   $s->checkUntil('/12/');        # -> "Fri Dec 12"
	 *   $s->pos;                       # -> 0
	 *   $s->matched;                   # -> 12
	 * @endcode
	 * Mnemonic: it "checks" to see whether a scanUntil will return a value.
	 * 
	 * @param string $pattern
	 * @return string
	 */
	public function checkUntil($pattern) {
		return $this->doScan($pattern, false, true, false);
	}

	/**
	 * Appends $str to the @mlink{$string} being scanned.
	 * This method does not affect scan @mlink{$pointer}.
	 * @code
	 *   $s = new StringScanner("Fri Dec 12 1975 14:39");
	 *   $s->scan('/Fri /');
	 *   $s->concat(" +1000 GMT");
	 *   $s->string;                // -> "Fri Dec 12 1975 14:39 +1000 GMT"
	 *   $s->scan('/Dec/');         // -> "Dec"
	 * @endcode
	 * @param $str
	 */
	public function concat($str)
	{
		$this->_string .= $str;
		$this->_string_updated(true);
	}
	
	/**
	 * Returns true if the scan @mlink{$pointer} is at the end of the string.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   echo $s->eos;          # => false
	 *   $s->scan('/test/');
	 *   echo $s->eos;          # => false
	 *   $s->terminate();
	 *   echo $s->eos;          # => true
	 * @endcode
	 * 
	 * @return bool
	 */
	protected function isEos() {
		return $this->_curr == $this->_size;
	}

	/**
	 * Looks ahead to see if the $pattern exists anywhere in the @mlink{$string}, without advancing the scan @mlink{$pointer}. 
	 * This predicts whether a {@link scanUntil()} will return a value.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->exist('/s/');            # -> 3
	 *   $s->scan('/test/');          # -> "test"
	 *   $s->exist('/s/');            # -> 2
	 *   $s->exist('/e/');            # -> null
	 * @endcode
	 * 
	 * @param string $pattern
	 */
	public function exist($pattern) {
		return $this->doScan($pattern, false, false, false);
	}
	
	/* Scath of getByte method. 
	 * Important!!!
	 * The implementation of this method affects getch() because using it can leave part of a multi-byte character in the $_rest of the string.
	 * 
	 * Scans one byte and returns it. This method is not multibyte character sensitive and will not work if mbstring.func_overload is set to ON in php.ini. 
	 * @code
	 *   $s = new StringScanner('ab')
	 *   $s->get_byte;         # => "a"
	 *   $s->get_byte;         # => "b"
	 *   $s->get_byte;         # => null
	 * 
	 *   $KCODE = 'EUC'
	 *   $s = new StringScanner("\244\242")
	 *   $s->get_byte;         # => "\244"
	 *   $s->get_byte;         # => "\242"
	 *   $s->get_byte;         # => null
	 * @endcode
	 * 
	 * @see getch()
	 * @return string
	 *
	public function getByte() {
		
	    $this->_matches = null;
	    
	    if ($this->_eos)
	        return null;
	
	    $this->prev = $this->curr;
	    $this->curr++;
	    
	    $this->_matches = array($this->_rest[$this->_prev]);
	    
	    return $this->_rest[$this->_prev];
	}
	*/
	
	/**
	 * Scans one character and returns it. This method is multibyte character sensitive.
	 * @code
	 *   $s = new StringScanner("ab");
	 *   $s->getch();           # => "a"
	 *   $s->getch();           # => "b"
	 *   $s->getch();           # => null
	 * 
	 *   $s = new StringScanner("\244\242");
	 *   $s->getch();           # => "\244\242"   # Japanese hira-kana "A" in EUC-JP
	 *   $s->getch();           # => null
	 * @endcode
	 */
	public function getch() {
		if($this->eos)
			return null;
			
		$ch = mb_substr($this->_rest, 0, 1);
		$this->_matches[0] = $ch;
		$this->_matched(true, MATCH_MODE_HEAD_ONLY);
		
		return $ch;
	}
	
	/**
	 * Returns a string that represents the StringScanner object, showing:
	 * - the current position
	 * - the size of the string
	 * - the characters surrounding the scan pointer
	 * @code
	 *   $s = new StringScanner("Fri Dec 12 1975 14:39");
	 *   $s->inspect();             # -> '#<StringScanner 0/21 @ "Fri D...">'
	 *   $s->scanUntil('/12/');     # -> "Fri Dec 12"
	 *   $s->inspect();             # -> '#<StringScanner 10/21 "...ec 12" @ " 1975...">'
	 * @endcode
	 */
	public function inspect() {
		$s = "#<StringScanner {$this->_curr}/{$this->_size} ";
		
		if($this->_curr > 0)
		{
			$s .= '"';
			if($this->_curr > 5)
				$s .= '...';
			$s .= mb_substr($this->_string, $this->_curr-5, 5).'" ';
		}
		
		$s .= '@ ';
	
		if($this->_curr < $this->_size)
		{
			$s .= '"'.mb_substr($this->_rest, 0, 5);
			if($this->_restSize > 5)
				$s .= '...';
			$s .= '">';
		}
		
		return $s;
	}

	/**
	 * Tests whether the given $pattern is matched from the current scan @mlink{$pointer}.
	 * Returns the length of the match, or <pre>null</pre>.  The scan @mlink{$pointer} is not advanced.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   echo $s->match('/\w+/');   # -> 4
	 *   echo $s->match('/\w+/');   # -> 4
	 *   echo $s->match('/\s+/');   # -> null
	 * @endcode
	 * 
	 * @param string $pattern
	 * @return int
	 */
	public function match($pattern) {
		return $this->doScan($pattern, false, false, true);
	}
	
	/**
	 * Returns true if the last match was successful.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->match('/\w+/');     # => 4
	 *   $s->matched();          # => true
	 *   $s->matched;            # => "test"
	 *   $s->match('/\d+/');     # => null
	 *   $s->matched();          # => false
	 * @endcode
	 * @return bool
	 */
	public function matched() {
		return isset($this->_matches[0]);
	}
	
	/**
	 * Returns the last matched string.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->match('/\w+/');     # -> 4
	 *   $s->matched;            # -> "test"
	 * @endcode
	 * @return string
	 */
	public function getMatched() {
		
		if(MATCH_MODE_HEAD_ONLY == $this->_lastMatchMode)
			return isset($this->_matches[0]) ? $this->_matches[0] : null;
		
		if(isset($this->_matches[1]))
			return $this->_matches[1];
		
		return null;
	}
	
	/**
	 * Returns the last matched string.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->match('/\w+/');     # -> 4
	 *   $s->matched;            # -> "test"
	 * @endcode
	 * @return string
	 */
	public function getScanned() {
		return isset($this->_matches[0]) ? $this->_matches[0] : null;
	}
	
	/**
	 * Returns the size of the most recent match (see matched), or null if there was no recent match.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->check('/\w+/');           # -> "test"
	 *   $s->matchedSize;              # -> 4
	 *   $s->check('/\d+/');           # -> null
	 *   $s->matchedSize;              # -> null
	 * @endcode
	 * @return int
	 */
	public function getMatchedSize() {
		if(isset($this->_matches[0]))
			return mb_strlen($this->_matches[0]);
		
		return null;
	}
	
	/**
	 * Extracts a string corresponding to <tt>mb_substr(@mlink{$rest}, 0, $len)</tt>, without
	 * advancing the scan pointer.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->peek(7)          # => "test st"
	 *   $s->peek(7)          # => "test st"
	 * @endcode
	 * 
	 * @param $len
	 * @return string
	 */
	public function peek($len)
	{
		if($this->eos)
			return null;
		
		return mb_substr($this->_rest, $this->_curr, $len);
	}
	
	/**
	 * Returns the character position of the scan @mlink{$pointer}.  In the '{@link reset() reset}' position, this
	 * value is zero.  In the '{@link terminate() terminated}' position (i.e. the string is exhausted),
	 * this value is the size of the @mlink{$string}.
	 * @magicalias{$pos, $pointer}
	 *
	 * In short, it's a 0-based index into the @mlink{$string}.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->pos;               # -> 0
	 *   $s->scanUntil /str/  # -> "test str"
	 *   $s->pos;               # -> 8
	 *   $s->terminate();         # -> #<StringScanner fin>
	 *   $s->pos;               # -> 11
	 * @endcode
	 * 
	 * @return int
	 */
	public function getPos() {
		return $this->_curr;
	}
	
	/**
	 * Set the byte position of the scan pointer.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->pos = 7            # -> 7
	 *   $s->rest;               # -> "ring"
	 * @endcode
	 * 
	 * @param int $i
	 * @return int The new position
	 */
	public function setPointer($i)
	{
	    if ($i < 0) 
	    	$i += $this->_restSize;
	    	
	    if ($i < 0 || $i > $this->_restSize) 
	    	throw new Exception("Index out of range.");
	    	
	    $this->_curr = $i;
	    
	    return $i;
	}
	
	/**
	 * Same as {@link setPointer()}
	 * Set the byte position of the scan pointer.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->pos = 7            # -> 7
	 *   $s->rest;               # -> "ring"
	 * @endcode
	 * 
	 * @param int $i
	 * @return int The new position
	 */
	public function setPos($i) {
		return $this->setPointer($i);
	}
	
	/**
	 * Return the <i><b>post</b>-match</i> (in the regular expression sense) of the last scan.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->scan('/\w+/');           # -> "test"
	 *   $s->scan('/\s+/');           # -> " "
	 *   $s->preMatch;             # -> "test"
	 *   $s->postMatch;            # -> "string"
	 * @endcode
	 * 
	 * @return string
	 */
	public function getPostMatch()
	{
		if(!isset($this->_matches[0]))
			return null;
			
		return mb_substr($this->_string, $this->_lastMatchEnd, $this->_size);
	}
	
	/**
	 * Return the <i><b>pre</b>-match</i> (in the regular expression sense) of the last scan.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->scan('/\w+/');           # -> "test"
	 *   $s->scan('/\s+/');           # -> " "
	 *   $s->preMatch;             # -> "test"
	 *   $s->postMatch;            # -> "string"
	 * @endcode
	 * 
	 * @return string
	 */
	public function getPreMatch()
	{
		if(!isset($this->_matches[0]))
			return null;
			
		return mb_substr($this->_string, $this->_prev, $this->_lastMatchBeg - $this->_prev);
	}
	
	/**
	 * Reset the scan pointer (index 0) and clear matching data.
	 */
	public function reset()
	{
		$this->_string_updated(false);
	}
	
	/**
	 * Returns the "rest" of the string (i.e. everything after the scan pointer).
	 * If there is no more data (eos? = true), it returns <tt>""</tt>.
	 * 
	 * @return string
	 */
	public function getRest()
	{
		return $this->_rest;
	}
	
	/**
	 * $s->restSize() is equivalent to mb_strlen($s->rest)
	 */
	public function restSize() {
		return $this->_restSize;
	}
	
	/**
	 * Tries to match with $pattern at the current position. If there's a match,
	 * the scanner advances the scan @mlink{$pointer} and returns the matched string.
	 * Otherwise, the scanner returns <pre>null</pre>.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   echo $s->scan('/\w+/');   # -> "test"
	 *   echo $s->scan('/\w+/');   # -> null
	 *   echo $s->scan('/\s+/');   # -> " "
	 *   echo $s->scan('/\w+/');   # -> "string"
	 *   echo $s->scan('/./');     # -> null
	 * @endcode
	 * 
	 * @param string $pattern
	 */
	public function scan($pattern)
	{
		return $this->doScan($pattern, true, true, true);
	}

	/**
	 * Tests whether the given +pattern+ is matched from the current scan pointer.
	 * Advances the scan pointer if +advance_pointer_p+ is true.
	 * Returns the matched string if +return_string_p+ is true.
	 * The match register is affected.
	 *
	 * "full" means "#scan with full parameters".
	 * 
	 * @todo Write test
	 * @param string $pattern The regular expression
	 * @param bool $advance_pointer Wheter to update the pointer or not
	 * @param bool $return_string Wheter to return the matched string or the position
	 * @return mixed Either the matched string (if $return_string is true) or the end position of the match
	 */
	public function scanFull($pattern, $advance_pointer, $return_string)
	{
		return $this->doScan($pattern, $advance_pointer, $return_string, true);
	}

	/**
	 * Scans the string _until_ the +pattern+ is matched.  Returns the substring up
	 * to and including the end of the match, advancing the scan pointer to that
	 * location. If there is no match, +null+ is returned.
	 * @code
	 *   $s = new StringScanner("Fri Dec 12 1975 14:39");
	 *   $s->scanUntil('/1/');        # -> "Fri Dec 1"
	 *   $s->preMatch();              # -> "Fri Dec "
	 *   $s->scanUntil('/XYZ/');      # -> null
	 * @endcode
	 * 
	 * @param string $pattern
	 * @return string The matched string
	 */
	public function scanUntil($pattern)
	{
		return $this->doScan($pattern, true, true, false);
	}

	/** 
	 * Scans the string _until_ the $pattern is matched.
	 * Advances the scan pointer if $advance_pointer, otherwise not.
	 * Returns the matched string if $return_string is true, otherwise
	 * returns the number of bytes advanced.
	 * This method does affect the match register.
	 * 
	 * @todo Write test
	 * @param string $pattern The regular expression
	 * @param bool $advance_pointer Wheter to update the pointer or not
	 * @param bool $return_string Wheter to return the matched string or the position
	 * @return mixed Either the matched string (if $return_string is true) or the end position of the match
	 */
	public function searchFull($pattern, $advance_pointer, $return_string)
	{
		return $this->doScan($pattern, $advance_pointer, $return_string, false);
	}

	/** 
	 * Attempts to skip over the given +pattern+ beginning with the scan pointer.
	 * If it matches, the scan pointer is advanced to the end of the match, and the
	 * length of the match is returned.  Otherwise, +null+ is returned.
	 *
	 * It's similar to #scan, but without returning the matched string.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   echo $s->skip('/\w+/');   # -> 4
	 *   echo $s->skip('/\w+/');   # -> null
	 *   echo $s->skip('/\s+/');   # -> 1
	 *   echo $s->skip('/\w+/');   # -> 6
	 *   echo $s->skip('/./');     # -> null
	 * @endcode
	 * 
	 * @param string $pattern
	 * @return int The new position
	 */
	public function skip($pattern)
	{
		return $this->doScan($pattern, true, false, true);
	}

	/** 
	 * Advances the scan pointer until +pattern+ is matched and consumed.  Returns
	 * the number of bytes advanced, or +null+ if no match was found.
	 *
	 * Look ahead to match +pattern+, and advance the scan pointer to the _end_
	 * of the match.  Return the number of characters advanced, or +null+ if the
	 * match was unsuccessful.
	 *
	 * It's similar to #scanUntil, but without returning the intervening string.
	 *@code
	 *   $s = new StringScanner("Fri Dec 12 1975 14:39");
	 *   $s->skipUntil('/12/');           # -> 10
	 * @endcode
	 * 
	 * @param string $pattern
	 * @return string The new pointer position
	 */
	public function skipUntil($pattern)
	{
		return $this->doScan($pattern, true, false, false);
	}
	
	/**
	 * Returns the string being scanned.
	 * 
	 * @return string
	 */
	public function getString() {
		return $this->_string;
	}

	/**
	 * Changes the string being scanned to +str+ and resets the scanner.
	 * 
	 * @param string $str
	 * @return string $str
	 */
	public function setString($str) {
		$this->_string = $str;
	    $this->reset();
    	return $str;
	}

	/**
	 * Set the scan pointer to the end of the string and clear matching data.
	 */
	public function terminate() {
    	$this->_curr = $this->_size;
    	$this->_clear_matched();
	}
	
	/**
	 * Set the scan pointer to the previous position.  Only one previous position is
	 * remembered, and it changes with each scanning operation.
	 * @code
	 *   $s = new StringScanner('test string');
	 *   $s->scan('/\w+/');        # => "test"
	 *   $s->unscan();
	 *   $s->scan('/../');         # => "te"
	 *   $s->scan('/\d/');         # => null
	 *   $s->unscan();             # ScanError: unscan failed: previous match record not exist
	 * @endcode
	 */
	public function unscan()
	{
		if(!isset($this->_matches[0]))
			throw new Exception("Unscan failed: previous match record not exist.");
			
		$this->_curr = $this->_prev;
		$this->_rest = mb_substr($this->_string, $this->_curr, $this->_size);
    	$this->_clear_matched();
	}
}