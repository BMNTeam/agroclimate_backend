import {Component, OnInit} from '@angular/core';
import {ConnectionService, Meteostation} from "../connection.service";
import {Form, FormControl} from "@angular/forms";
import {ActivatedRoute, Router} from "@angular/router";

enum Mode {Edit, View};

export interface Request {
    meteostationId: number,
    yearStart: number,
    yearEnd?: number,
    yearToEdit?: number
}

@Component({
    selector: 'app-select-data',
    templateUrl: './select-data.component.html',
    styleUrls: ['./select-data.component.sass'],
    providers: [ConnectionService]
})
export class SelectDataComponent implements OnInit {

    editing: boolean;

    request: Request;

    mode: Mode = Mode.View;

    meteostations: Meteostation[];

    constructor(private connectionService: ConnectionService,
                private router: Router,
                private route: ActivatedRoute
    ) {
        this.generateRequest();
    }

    ngOnInit() {
        this.getMeteostationList();

    }

    private getMeteostationList() {
        this.connectionService.getMeteostationsList('all').subscribe( (val) => {
            this.meteostations = val;
            this.addCustomZones(this.meteostations);

        })

    }
    private  addCustomZones(meteostations: Meteostation[]): Meteostation[]
    {
        meteostations.push(
            {ID: 'first_zone', Name: 'Зона 1'},
            {ID: 'second_zone', Name: 'Зона 2'},
            {ID: 'third_zone', Name: 'Зона 3'},
            {ID: 'fourth_zone', Name: 'Зона 4'}
        );
        return meteostations;

    }

    submit(form: FormControl)
    {
        if(form.value.yearToEdit) this.mode = Mode.Edit;

        let formData = form.value as Request;

        if(this.mode = Mode.View) this.showTPData(formData);
        //if(this.mode = Mode.Edit) this.setTPData();
    }
    private showTPData(formData: Request)
    {
        if(!formData.yearEnd) {
            this.router.navigate(['/show', formData.meteostationId, formData.yearStart ]);
            return;
        }
        this.router.navigate(['/show', formData.meteostationId, formData.yearStart, formData.yearEnd ])

    }


    generateRequest()
    {
        this.request = {
            meteostationId: null,
            yearStart: null,
            yearEnd: null,
            yearToEdit: null
        };

        this.route.params.subscribe(
            params => {
                this.request = {
                    meteostationId: params.meteostationId,
                    yearStart: params.yearStart
                }
            }
        );


    }

}
