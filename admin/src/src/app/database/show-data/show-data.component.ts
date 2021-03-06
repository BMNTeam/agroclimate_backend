import {Component, OnInit} from '@angular/core';
import {Request} from "../select-data/select-data.component";
import {ActivatedRoute, Router} from "@angular/router";
import {ConnectionService, Meteostation} from "../connection.service";

interface showRequest extends Request {
    mode: string,
    custom?: string
}

interface WeatherInfo {
    Year: number,
    MeteostationID: number,
    T1: number,
    T2: number,
    T3: number,
    T4: number,
    T5: number,
    T6: number,
    T7: number,
    T8: number,
    T9: number,
    T10: number,
    T12: number,
    P1: number,
    P2: number,
    P3: number,
    P4: number,
    P5: number,
    P6: number,
    P7: number,
    P8: number,
    P9: number,
    P10: number,
    P11: number,
    P12: number
}

@Component({
    selector: 'app-show-data',
    templateUrl: './show-data.component.html',
    styleUrls: ['./show-data.component.sass'],
    providers: [ConnectionService]
})
export class ShowDataComponent implements OnInit {
    request: showRequest;
    info: WeatherInfo[];
    editable: boolean = true;

    meteostation: string;

    constructor(private connectionService: ConnectionService,
                private route: ActivatedRoute,
                private router: Router) {
    }

    ngOnInit() {
        this.route.params.subscribe(
            params => {

                this.request = {
                    meteostationId: params.meteostationId,
                    yearStart: params.yearStart,
                    yearEnd: params.yearEnd,
                    mode: 'plural'
                };

                this.setMeteostation(params.meteostationId);
                this.getTPData(this.request);
            }
        )

    }

    private getTPData(data: showRequest) {
        if (!data.yearEnd) {
            data.mode = 'single';
            data.yearEnd = 0; // Use in Average class
        }
        if (isNaN(data.meteostationId)) { //if custom zone
            data.mode = 'custom';
        }
        this.connectionService.getTP(data).subscribe(
            res => this.info = res
        )
    }

    public getAverage(item: Object): number {
        let count = 0;
        let sum = 0;

        for (let key in item) {
            if (key.indexOf('T') === -1 || !item[key]) continue; //last check for 'null'
            sum += parseFloat(item[key]);
            count++;

        }

        return Math.floor(sum / count * 100) / 100;
    }

    public getSum(item: Object): number {
        let sum = 0;

        for (let key in item) {
            if (key.indexOf('P') === -1 || !item[key]) continue;
            sum += parseFloat(item[key]);
        }
        return Math.floor(sum * 100) / 100;
    }

    private setMeteostation(id: number) {
        this.connectionService.getMeteostationsList('all').subscribe(
            i => {
                let meteostation: Meteostation = i.filter(i => i.ID == id)[0];
                if (!meteostation) {
                    this.meteostation = this.getCustomZone(id.toString());
                    this.editable = false;
                } else {
                    this.meteostation = meteostation.Name;
                }

            }
        )

    }

    private getCustomZone(id: string) :string
    {
        switch (id) {
            case "region":
                return "Регион";
            case "first_zone":
                return "Первая зона";
            case "second_zone":
                return  "Вторая зона";
            case "third_zone":
                return "Третья зона";
            case "fourth_zone":
                return "Четвертая зона"
        }
    }

    private edit(meteostationId: string, year: number)
    {
        this.router.navigate(['edit', meteostationId, year]);
    }

    public back(): void {
        this.router.navigate(['', this.request.meteostationId, this.request.yearStart]);
    }

}
