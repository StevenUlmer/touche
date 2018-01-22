import { Component, OnInit } from '@angular/core';
import { TableColumn } from '../../../models/table_column';
import { Standing } from '../../../models/standing';
import { StandingService } from '../../../services/model_services/standing.service';
import { JudgeStandingProblemsComponent } from './problems/problems.component';
import { JudgeStandingFinalScoreComponent } from './final_score/final_score.component';

@Component({
    templateUrl: './standings.component.html'
})
export class JudgeStandingsComponent {
    tableColumns: TableColumn[] = [
        { header: 'Rank', dataField: 'rank', displayIsComponent: false, component: null },
        { header: 'Team', dataField: 'teamName', displayIsComponent: false, component: null },
        { header: 'Problems', dataField: 'problemsCompleted', displayIsComponent: true, component: JudgeStandingProblemsComponent },
        { header: 'Final Score', dataField: 'rank', displayIsComponent: true, component: JudgeStandingFinalScoreComponent }
    ];
    standings: Standing[];

    filters = [
        {value: 'filter-0', viewValue: 'All Teams'},
        {value: 'filter-1', viewValue: 'Team 1'},
        {value: 'filter-2', viewValue: 'Team 2'},
        {value: 'filter-3', viewValue: 'Team 3'}
    ];

    constructor(private service: StandingService) { }

    ngOnInit() {
        this.standings = this.service.getMockData();
    }
}