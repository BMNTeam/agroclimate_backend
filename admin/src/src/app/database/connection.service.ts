import {Injectable} from '@angular/core';
import {HttpClient, HttpParams} from "@angular/common/http";
import {environment} from "../../environments/environment";
import {Observable} from "rxjs/Observable";
import {Subject} from "rxjs/Subject";
import {HttpParamsOptions} from "@angular/common/http/src/params";


export interface Meteostation {
    ID: number | string,
    Name: string
}

export interface DecadesGetParams {
    yearStart: number,
    meteostationId: number

}

@Injectable()
export class ConnectionService {
    meteostations: Subject<Meteostation[]>;

    constructor(private http: HttpClient) {
        this.setMeteostations();
        this.meteostations = new Subject<Meteostation[]>()
    }

    public getMeteostationsList(param: string): Observable<Meteostation[]>
    {
        return this.http.get<Meteostation[]>(environment.URL + 'routes/meteostations.php' + `?${param}`);
    }

    private setMeteostations(): void
    {
        this.getMeteostationsList('all').subscribe(
            res => this.meteostations.next(res)
        )
    }

    public getDecadesData(reqParams: DecadesGetParams)
    {
        let params = {
            yearStart: reqParams.yearStart.toString(),
            meteostationId: reqParams.meteostationId.toString()
        };
        return this.http.get(environment.URL + 'routes/decades_tp.php?mode=edit', {params})
    }

    public getTP(data: any): Observable <any>
    {
        let params = data;
        return this.http.get(environment.URL + 'routes/climate_tp.php', {
            params: params
        });
    }
}
