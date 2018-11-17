<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AdminActor extends AcceptanceTester
{
    /**
     * AdminActor constructor.
     * @param \Codeception\Scenario $scenario an opaque object that codeception will automatically pass in
     */
    public function __construct(\Codeception\Scenario $scenario)
    {
        parent::__construct($scenario, "adminAttr.ini");
    }

    /**
     * @param $teamName string name of the team that's being created. May contain spaces.
     * @param $organization string name of the team's organization.
     * @param $username string username for the new team
     * @param $password string password for the new team. Overrides the autogenerated password.
     * @param $siteId int The index of the site. 0 is a safe choice.
     * @param $contestant1 string name of the first team member. No specific formatting required
     * @param $contestant2 string name of the second team member
     * @param $contestant3 string name of the third team member
     * @param $alternate string name of the alternate team member
     * @param $email string email for the team. Not currently used but may be in future
     * @param $coach string name of the coach
     */
	public function addTeam($teamName, $organization, $username, $password, $siteId,
                            $contestant1, $contestant2, $contestant3, $alternate, $email, $coach)
	{
		$I = $this;
		$I->amOnMyPage("setup_teams.php");
		$I->fillField('team_name', $teamName);
        $I->fillField('organization', $organization);
        $I->fillField('username', $username);
        $I->fillField('password', $password);
        $I->selectOption('site_id', $siteId);
        $I->fillField('contestant_1_name', $contestant1);
        $I->fillField('contestant_2_name', $contestant2);
        $I->fillField('contestant_3_name', $contestant3);
        $I->fillField('alternate_name', $alternate);
        $I->fillField('email', $email);
        $I->fillField('coach_name', $coach);
        $I->click('submit');
	}

    /**
     * @param $siteName string name of the site to add
     */
	public function addSite($siteName)
	{	
		$I = $this;
		$I->amOnMyPage('setup_site.php');
        $I->fillField('site_name', $siteName);
        $I->click('submit');
	}
}