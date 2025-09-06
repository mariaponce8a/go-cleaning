import { HttpClient } from '@angular/common/http';
import { Injectable, EventEmitter } from '@angular/core';
import * as CryptoJS from "crypto-js";

@Injectable({
  providedIn: 'root',
})
export class LocalStorageEncryptationService {
  statustoggle: EventEmitter<any> = new EventEmitter();
  btnclose: EventEmitter<any> = new EventEmitter();

  private sortingData: string = "PROUYBFECJHSBVSDHJVB";
  constructor(
    public http: HttpClient,
  ) {}

  getLocalStorage(key: string): string | null {
    let info = localStorage.getItem(key);
    if (info) {
      let firstDecrypt = this.decrypt(info);
      return firstDecrypt;
    }
    return null;
  }
 

  setLocalStorage(key: string, value: string) {
    let encryptValue = this.encrypt(value);
    localStorage.setItem(key, encryptValue);
  }

  remove(key: string) {
    localStorage.removeItem(key);
  }

  private encrypt(word: string) {
    let encryptedText = CryptoJS.AES.encrypt(word, this.sortingData).toString();
    return encryptedText;
  }

  private decrypt(word: string) {
    const bytes = CryptoJS.AES.decrypt(word, this.sortingData);
    let decriptedText = bytes.toString(CryptoJS.enc.Utf8);
    return decriptedText;
  }

}
