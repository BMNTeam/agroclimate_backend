import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, Router} from "@angular/router";
import {ConnectionService} from "../connection.service";


interface EditRequest {
    MeteostationID: number,
    Year: number
}

@Component({
    selector: 'app-edit-data',
    templateUrl: './edit-data.component.html',
    styleUrls: ['./edit-data.component.sass'],
    providers: [ConnectionService]
})
export class EditDataComponent implements OnInit {
    request: EditRequest;
    year: number;

    //TODO: fix external module meteostations
    meteostation: string;

    constructor(private router: Router,
                private route: ActivatedRoute,
                private connectionSrv: ConnectionService
                )
    {
        this.route.params.subscribe( res => {
            this.request = {
                MeteostationID: res.meteostationId,
                Year: res.yearStart
            };

            this.year = res.yearStart;
        });

    }

    ngOnInit()
    {
    }

    private back(): void {
        this.router.navigate(['show', this.request.MeteostationID, this.request.Year]);
    }

    private home(): void
    {
        this.router.navigate(['', this.request.MeteostationID, this.request.Year]);
    }
}
