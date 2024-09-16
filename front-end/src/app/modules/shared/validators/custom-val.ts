import { AbstractControl, ValidationErrors, ValidatorFn, FormGroup } from '@angular/forms';

// Validador personalizado para fechas
export function validarFechas(): ValidatorFn {
  return (formGroup: AbstractControl): ValidationErrors | null => {
    const fechaRecoleccion = formGroup.get('fecha_recoleccion_estimada')?.value;
    const fechaEntrega = formGroup.get('fecha_entrega_estimada')?.value;
    const fechaActual = new Date().setHours(0, 0, 0, 0); // Fecha actual sin horas

    // Si no hay fechas, no se realiza la validación
    if (!fechaRecoleccion || !fechaEntrega) {
      return null;
    }

    // Convertir fechas a objetos Date para compararlas
    const recolectarFecha = new Date(fechaRecoleccion).setHours(0, 0, 0, 0);
    const entregarFecha = new Date(fechaEntrega).setHours(0, 0, 0, 0);

    // Validar que la fecha de recolección no sea anterior a la actual
    if (recolectarFecha < fechaActual) {
      return { fechaRecoleccionInvalida: true };
    }

    // Validar que la fecha de entrega sea mayor que la de recolección
    if (entregarFecha <= recolectarFecha) {
      return { fechaEntregaInvalida: true };
    }

    return null; // Valido si pasa todas las condiciones
  };
}

