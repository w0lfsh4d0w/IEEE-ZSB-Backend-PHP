<?php
use core\Validator ;

it('validates a string', function () {

    expect(\core\Validator::string('foobar'))->toBeTrue();
    expect(\core\Validator::string(false))->toBeFalse();
    expect(\core\Validator::string(''))->toBeFalse();
});

it('validates a string with minumum length', function () {

    expect(\core\Validator::string('foobar',20))->toBeFalse();

});


it('validates an  email', function () {

    expect(\core\Validator::email('foobar'))->toBeFalse();
    expect(\core\Validator::email('foobar@example.com'))->toBeTrue();
    
});

it("validate that a number is gratet than a give amount ",function ()
{
    expect(Validator::graterThan(10,1))->toBeTrue() ;
    expect(Validator::graterThan(10,100))->toBeFalse() ;
});