import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, Router} from "@angular/router";
import {ConnectionService, DecadesGetParams} from "../connection.service";



export interface EditRequest {
    MeteostationID: number,
    Year: number,
    [key: string]: number | string
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
    meteostation: string;

    months: string[];

    constructor(private router: Router,
                private route: ActivatedRoute,
                private connectionSrv: ConnectionService
                )
    {

    }

    ngOnInit()
    {
        this.initDefault();
        this.months = this.getMonths();
        this.getData()
    }

    private initDefault(): void
    {
        this.route.params.subscribe( route => {
            this.request = {
                MeteostationID: route.meteostationId,
                Year: route.yearStart
            };

            this.year = route.yearStart;
            this.connectionSrv.meteostations
                .subscribe(res => {
                   if (!res.filter( i => i.ID === +route.meteostationId)[0] ) return;
                   this.meteostation = res.filter( i => i.ID === +route.meteostationId)[0].Name
                }
            )
        });
    }


    private getData()
    {
        let params: DecadesGetParams = {
            yearStart: +this.request.Year,
            meteostationId: +this.request.MeteostationID
        };

        this.connectionSrv.getDecadesData(params).subscribe(
            res => {
               Object.keys(res[0]).forEach(i => this.request[i] = res[0][i])
            }
        )
    }


    private getMonths(): string[]
    {
        return ['Январь','Февраль', "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"]
    }

    private back(): void {
        this.router.navigate(['show', this.request.MeteostationID, this.request.Year]);
    }

    private home(): void
    {
        this.router.navigate(['', this.request.MeteostationID, this.request.Year]);
    }

    private reqToNum()
    {
        Object.keys(this.request).map(
            i => {
                if(this.request[i]) this.request[i] = +this.request[i]; //Skip empty values
            }
        );
    }
    public submit()
    {

        this.reqToNum();
        this.connectionSrv.saveDecadesData(this.request).subscribe(
            res => this.back()
        )
    }

    /**
     * Validate key | continue if key are: Number, Dot, Backspace, Tab
     * @param {KeyboardEvent} event
     */
    public validate(event: KeyboardEvent)
    {
        if(event && event.key.match(/\d/) ||
            (event.key == '.' || event.keyCode == 8 || event.keyCode == 9) ) return;
        event.preventDefault();
    }
}
