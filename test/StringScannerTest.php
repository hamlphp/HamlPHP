<?php

require_once 'test_helper.php';

require_once HAMLPHP_DIR_SRC . "/util/StringScanner.php";

class StringScannerTest extends PHPUnit_Framework_TestCase
{
	public function testScan()
	{
		$s = new StringScanner("Fri Dec 12 1975 14:39");
		$m = $s->scan('/(\w+) (\w+) (\d+) /');
		$this->assertEquals("Fri Dec 12 ", $m);

		$s = new StringScanner("Fri Dec 12 1975 14:39");
		$this->assertEquals("Fri ", $s->scan('/Fri /'));
		$this->assertEquals("Dec", $s->scan('/Dec/'));
		$this->assertEquals(" 12", $s->scan('/\s12/'));
		$this->assertEquals(" 1975 14", $s->scan('/[0-9\s]+/'));
		$this->assertEquals(":39", $s->scan('/\:39/'));
	    $this->assertNull($s->scan('/./'));
		$this->assertTrue($s->eos);
	}

	public function testArrayAccess()
	{
		$s = new StringScanner("Fri Dec 12 1975 14:39");
		$m = $s->scan('/(\w+) (\w+) (\d+) /');
		$this->assertEquals("Fri Dec 12 ", $m, "Scan failed so testing ArrayAccess makes no sense.");
		$this->assertEquals("Fri Dec 12 ", $s[0]);
		$this->assertEquals("Fri", $s[1]);
		$this->assertEquals("Dec", $s[2]);
		$this->assertEquals("12", $s[3]);
	}

	public function testBol()
	{
		$s = new StringScanner("test\ntest\n");
		$this->assertTrue($s->bol);
		$s->scan('/te/');
		$this->assertFalse($s->bol);
		$s->scan("/st\n/");
		$this->assertTrue($s->bol);
		$s->terminate();
		$this->assertTrue($s->bol);
	}

	public function testCheck()
	{
		$s = new StringScanner("Fri Dec 12 1975 14:39");
	    $this->assertEquals('Fri', $s->check('/Fri/'));
	    $this->assertEquals(0, $s->pos);
	    $this->assertEquals('Fri', $s->matched);
	    $this->assertNull($s->check('/12/'));
	    $this->assertNull($s->matched);
	}

	public function testCheckUntil()
	{
		$s = new StringScanner("Fri Dec 12 1975 14:39");
	    $this->assertEquals("Fri Dec 12", $s->checkUntil('/12/'));
	    $this->assertEquals(0, $s->pos);
	    $this->assertEquals("12", $s->matched);
	}

	public function testConcat() {
		$s = new StringScanner("Fri Dec 12 1975 14:39");
	    $s->scan('/Fri /');
	    //$s->concat(" +1000 GMT");
	    //$this->assertEquals("Fri Dec 12 1975 14:39 +1000 GMT", $s->string);
	    $this->assertEquals("Dec", $s->scan('/Dec/'));
	}

	public function testEos() {
		$s = new StringScanner('test string');
	    $this->assertFalse($s->eos);
	    $s->scan('/test/');
	    $this->assertFalse($s->eos);
	    $s->terminate();
	    $this->assertTrue($s->eos);
	}

	public function testExist() {
	    $s = new StringScanner('test string');
	    $this->assertEquals(3, $s->exist('/s/'));
	    $s->scan('/test/');
	    $this->assertEquals(2, $s->exist('/s/'));
	    $this->assertNull($s->exist('/e/'));
	}

	public function testGetch() {
		$s = new StringScanner("ab");
	    $this->assertEquals('a', $s->getch());
	    $this->assertEquals('b', $s->getch());
	    $this->assertNull($s->getch());

	    $s = new StringScanner("\xC2\xA2", "utf-8");
	    $this->assertEquals("\xC2\xA2", $s->getch());
	    $this->assertNull($s->getch());
	}

	public function testInspect() {
		$s = new StringScanner("Fri Dec 12 1975 14:39");
	    $this->assertEquals('#<StringScanner 0/21 @ "Fri D...">', $s->inspect());
	    $s->scanUntil('/12/');    # -> "Fri Dec 12"
	    $this->assertEquals(10, $s->pos);
	    $this->assertEquals('#<StringScanner 10/21 "...ec 12" @ " 1975...">', $s->inspect());
	}

	public function testMatch() {
		$s = new StringScanner('test string');
	    $this->assertEquals(4, $s->match('/\w+/'));
	    $this->assertEquals(4, $s->match('/\w+/'));
	    $this->assertNull($s->match('/\s+/'));
	}

	public function testMatchedAndGetMatched(){
	    $s = new StringScanner('test string');
	    $this->assertEquals(4, $s->match('/\w+/'));
	    $this->assertEquals('test', $s->matched);
	    $this->assertTrue($s->matched());
	    $this->assertNull($s->match('/\d+/'));
	    $this->assertNull($s->matched);
	    $this->assertFalse($s->matched());
	}

	public function testMatchedSize() {
		$s = new StringScanner('test string');
	    $this->assertEquals('test', $s->check('/\w+/'));      # -> "test"
	    $this->assertEquals(4, $s->matchedSize);              # -> 4
	    $this->assertNull($s->check('/\d+/'));                # -> null
	    $this->assertNull($s->matchedSize);                   # -> null
	}

	public function testPeek() {
		$s = new StringScanner('test string');
	    $this->assertEquals("test st", $s->peek(7));
	    $this->assertEquals("test st", $s->peek(7));
	}

	public function testPreAndPostMatch() {
		$s = new StringScanner('test string');
	    $s->scan('/\w+/');           # -> "test"
	    $s->scan('/\s+/');           # -> " "
	    $this->assertEquals('test', $s->preMatch);
	    $this->assertEquals('string', $s->postMatch);
	}

	public function testScanUntil() {
		$s = new StringScanner("Fri Dec 12 1975 14:39");
	    $this->assertEquals("Fri Dec 1", $s->scanUntil('/1/'));
	    $this->assertEquals("Fri Dec ", $s->preMatch);
	    $this->assertNull($s->scanUntil('/XYZ/'));
	}

	public function testSkipAndSkipUntil() {
		$s = new StringScanner('test string');
	    $this->assertEquals(4, $s->skip('/\w+/'));
	    $this->assertNull($s->skip('/\w+/'));
	    $this->assertEquals(1, $s->skip('/\s+/'));
	    $this->assertEquals(6, $s->skip('/\w+/'));
	    $this->assertNull($s->skip('/./'));

	    $s = new StringScanner("Fri Dec 12 1975 14:39");
	    $this->assertEquals(10, $s->skipUntil('/12/'));
	}

	public function testUnscan() {
		$s = new StringScanner('test string');
	    $this->assertEquals('test', $s->scan('/\w+/'));
	    $s->unscan();
	    $this->assertEquals('te', $s->scan('/../'));
	    $this->assertNull($s->scan('/\d/'));
	    try {
		    $s->unscan();             # ScanError: unscan failed: previous match record not exist
	    }
	    catch(Exception $e)
	    {
	    }

	    $this->assertNotNull($e);

	    $s->reset();
	    $this->assertEquals('test ', $s->scan('/\w+\s/'));
	    $this->assertEquals('st', $s->scan('/st/'));
	    $this->assertEquals('ri', $s->scan('/ri/'));
	    $s->unscan();
	    $this->assertEquals(7, $s->pos);
	    $this->assertEquals('ri', $s->scan('/ri/'));
	}
}
