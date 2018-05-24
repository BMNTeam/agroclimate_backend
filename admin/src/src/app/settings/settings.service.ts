import {Injectable, OnInit} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {environment} from "../../environments/environment";
import {Observable} from "rxjs/Observable";
import "rxjs/add/operator/map";
import {Subject} from "rxjs/Subject";

export interface Settings {
    maintenance: boolean;
}

@Injectable()
export class SettingsService implements OnInit {
    settings: Subject<Settings> = new Subject<Settings>();

    constructor(private http: HttpClient) {
        this.get().subscribe(i => {
            this.settings.next(i);
        })

    }

    ngOnInit() {
    }

    get(): Observable<Settings> {
        return this.http
            .get<Settings>(environment.URL + 'routes/settings.php?all')
        //.map(i => JSON.parse(i.toString()));
    }

    // TODO: find solution with post request, body and params;
    set(settings: Settings): void {
        const options = {settings}
        this.http.post(environment.URL + 'routes/settings.php', options,
            {headers: {'Content-Type': 'application/json'}}
        ).subscribe();
    }


}
