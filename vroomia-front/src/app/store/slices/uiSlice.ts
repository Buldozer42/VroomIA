import { createSlice, PayloadAction } from "@reduxjs/toolkit";

export type DrawerType = "profile" | "stack" | "stepper" | "logout";

interface UiState {
    drawerOpen: boolean;
    drawerType: DrawerType | null;
    selectedTab: DrawerType | null;
  }

const initialState: UiState = {
    drawerOpen: false,
    drawerType: null,
    selectedTab: null,
};

const uiSlice = createSlice({
    name: "ui",
    initialState,
    reducers: {
        openDrawer: (state, action: PayloadAction<DrawerType>) => {
            state.drawerOpen = true;
            state.drawerType = action.payload;
            state.selectedTab = action.payload;  // on met à jour selectedTab aussi ici
          },
        closeDrawer: (state) => {
            state.drawerOpen = false;
            state.drawerType = null;
        },
        setTab: (state, action: PayloadAction<DrawerType>) => {
            state.selectedTab = action.payload;
        },
    },
});

export const { openDrawer, closeDrawer, setTab } = uiSlice.actions;
export default uiSlice.reducer;
