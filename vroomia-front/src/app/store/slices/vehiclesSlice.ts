import { createSlice, PayloadAction } from "@reduxjs/toolkit";

// Définition du type Vehicle
export type Vehicle = {
  immatriculation: string;
  marque: string;
  model: string;
  year: number;
  vin: string;
  mileage: number;
  lastTechnicalInspectionDate: Date;
};

// État initial
const initialState: Vehicle[] = [
  {
    immatriculation: "BF-678-AF",
    marque: "BMW",
    model: "Série 8",
    year: 2018,
    vin: "XXX",
    mileage: 12500,
    lastTechnicalInspectionDate: new Date("2024-06-17"),
  },
];

const vehicleSlice = createSlice({
  name: "vehicle",
  initialState,
  reducers: {
    setVehicles: (state, action: PayloadAction<Vehicle[]>) => {
      return action.payload;
    },
    addVehicle: (state, action: PayloadAction<Vehicle>) => {
      state.push(action.payload);
    },
    updateVehicle: (state, action: PayloadAction<{ index: number; data: Vehicle }>) => {
      state[action.payload.index] = action.payload.data;
    },
    removeVehicle: (state, action: PayloadAction<number>) => {
      state.splice(action.payload, 1);
    },
  },
});

export const { setVehicles, addVehicle, updateVehicle, removeVehicle } = vehicleSlice.actions;
export default vehicleSlice.reducer;
