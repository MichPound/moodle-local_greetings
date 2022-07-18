<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/greetings/lib.php');

class lib_test extends \advanced_testcase {
    
    /**
     * Testing the translation of greeting messages.
     *
     * @dataProvider local_greetings_get_greeting_provider
     * @param string|null $country User country
     * @param string $langstring Greetings message language string
     */
    public function test_local_greetings_get_greeting(?string $country, string $langstring) {
        $user = null;
        if (!empty($country)) {
            $this->resetAfterTest(true);
            $user = $this->getDataGenerator()->create_user();
            $user->country = $country;
        }

        $this->assertSame(get_string($langstring, 'local_greetings', fullname($user)), local_greetings_get_greeting($user));
    }

    /**
     * Data provider for {@see test_local_greetings_get_greeting()}.
     *
     * @return array List of data sets - (string) data set name => (array) data
     */
    public function local_greetings_get_greeting_provider() {
        return [
            'No user' => [
                'country' => null,
                'langstring' => 'greetinguser'
            ],
            'AU user' => [
                'country' => 'AU',
                'langstring' => 'greetinguserau'
            ],
            'ES user' => [
                'country' => 'ES',
                'langstring' => 'greetinguseres'
            ],
            'VU user' => [
                'country' => 'VU',
                'langstring' => 'greetingloggedinuser'
            ],
        ];
    }
}