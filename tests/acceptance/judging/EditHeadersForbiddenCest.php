<?php


class EditHeadersForbiddenCest
{
    # Valid source file extensions.
    private static $src_extensions_headers = array("C" => ".c", "Java" => ".java", "C++" => ".cpp");

    private static $src_extensions_forbidden = array("C" => ".c", "C++" => ".cpp");

    private static $top_dir = "example_submissions";

    # These languages are checked for forbidden words
    private static $forbidden_word = array("C", "C++");

    # A list of teams that will be submitting problems. Populated in createTeams
    private $teams = array();

    /**
     * Uses several of the constants declared above to populate the list of teams with TeamActors
     * @param AdminActor $admin will be used to create the team in the contest
     * @param \Codeception\Scenario $scenario Needed to construct TeamActors
     */
    private function createTeams(AdminActor $admin, \Codeception\Scenario $scenario, $src_extensions){
        foreach ($src_extensions as $lang_name => $extension){
            $admin->attrLogin();

            $teamName = "$lang_name team";
            $username = $lang_name;
            $password = "password";
            $admin->addSimpleTeam($teamName, $username, $password);

            $team = new TeamActor($scenario);
            $team->attr["name"] = $teamName;
            $team->attr["username"] = $username;
            $team->attr["password"] = $password;
            $team->attr["forbidden_word"] = in_array($lang_name, self::$forbidden_word);
            $team->attr["extension"] = $extension; # The file extension this team submits

            array_push($this->teams, $team);
        }
    }

    /**
     * Deletes all the teams and re-creates the default team.
     * @param AdminActor $admin some admin actor
     */
    private function deleteTeams(AdminActor $admin){
        $admin->attrLogin();
        foreach ($this->teams as $_){
            $admin->deleteTeam();
        }
        $admin->deleteTeam();
        $admin->addDefaultTeam();

        $this->teams = array();
    }

    /**
     * Wrapper around TeamActor->submitSolution
     * @param TeamActor $I one of the elements of the $teams array
     * @param string $sub_dir a subdirectory listed in dirs_results
     */
    private function submitSolution(TeamActor $I, $sub_dir){
        $I->attrLogin();
        $extension = $I->attr['extension'];

        $path = self::$top_dir . "/$sub_dir/src$extension";
        $I->submitSolution($path);
        $I->see("Queued for judging");
    }

    /**
     * Checks to see if the desired judgment is the only visible auto judgement
     * @param JudgeActor $I must be logged in
     * @param bool $status true if accepted is desired; false otherwise
     */
    private function assertJudgmentsMatch(JudgeActor $I, $status){
        $I->amOnMyPage("judge.php");
        if($status == true)
            $I->see("Accepted");
        else
            $I->dontSee("Accepted");
    }

    /**
     * Submit several files from different teams and judge them
     * @param JudgeActor $judge does not need to be logged in
     * @param \Codeception\Scenario $scenario opaque variable passed through for creating a team actor
     * @param $acceptBool true if accepted is desired; false otherwise
     */
    private function submitBatch(JudgeActor $judge, \Codeception\Scenario $scenario, $acceptBool, $dir)
    {
        $num_submissions = 0;
        $wait_per_submission = 13; # How long each submission takes to be judged
        foreach ($this->teams as $team){
            $this->submitSolution($team, $dir);
            $num_submissions++;
        }
        $judge->attrLogin();
        $judge->waitForAutoJudging($wait_per_submission * $num_submissions, 75);

        $this->assertJudgmentsMatch($judge, $acceptBool);
        for (; $num_submissions > 0; $num_submissions--) {
            $judge->judgeSubmission();
        }
    }
    
    public function invalidHeaderFiles(AdminActor $admin, JudgeActor $judge, \Codeception\Scenario $scenario)
    {
        $judge->wantTo("Judge files with invalid header files");
        $this->createTeams($admin, $scenario, self::$src_extensions_headers);
        $this->submitBatch($judge, $scenario, false, "additional_headers");
    }

    public function addHeaders(AdminActor $I)
    {
        $I->wantTo("Add headers to make more solutions valid");
        $I->addHeader("C","float.h");
        $I->see("Header changed successfully");
        $I->addHeader("CXX","cfloat");
        $I->see("Header changed successfully");
        $I->addHeader("JAVA","java.text.*");
        $I->see("Header changed successfully");
    }

    public function validHeaderFiles(JudgeActor $judge, \Codeception\Scenario $scenario)
    {
        $judge->wantTo("Judge files with now valid header files");
        $this->submitBatch($judge, $scenario, true, "additional_headers");
    }

    public function deleteHeaders(AdminActor $I)
    {
        $I->wantTo("Delete headers to return to default");
        $I->deleteHeader("C","float.h");
        $I->see("Header changed successfully");
        $I->deleteHeader("CXX","cfloat");
        $I->see("Header changed successfully");
        $I->deleteHeader("JAVA","java.text.*");
        $I->see("Header changed successfully");
        $this->deleteTeams($I);
    }

    public function forbiddenWordsPresent(AdminActor $admin, JudgeActor $judge, \Codeception\Scenario $scenario)
    {
        $judge->wantTo("Judge files with forbidden words and have them fail");
        $this->createTeams($admin, $scenario, self::$src_extensions_forbidden);
        $this->submitBatch($judge, $scenario, false, "forbidden_word");
    }

    public function deleteForbiddenWords(AdminActor $I)
    {
        $I->wantTo("Delete forbidden words to test the system");
        $I->deleteForbiddenWord("C", "system");
        $I->see("Forbidden Word changed successfully");
        $I->deleteForbiddenWord("CXX", "open");
        $I->see("Forbidden Word changed successfully");
        $I->deleteForbiddenWord("CXX", "fstream");
        $I->see("Forbidden Word changed successfully");
    }

    public function forbiddenWordRemoved(JudgeActor $judge, \Codeception\Scenario $scenario)
    {
        $judge->wantTo("Judge files with forbidden words and have them succeed");
        $this->submitBatch($judge, $scenario, true, "forbidden_word");
    }

    public function addForbiddenWords(AdminActor $I)
    {
        $I->wantTo("Add forbidden words back to reset to default");
        $I->addForbiddenWord("C", "system");
        $I->see("Forbidden Word changed successfully");
        $I->addForbiddenWord("CXX", "open");
        $I->see("Forbidden Word changed successfully");
        $I->addForbiddenWord("CXX", "fstream");
        $I->see("Forbidden Word changed successfully");
        $this->deleteTeams($I);
    }
}