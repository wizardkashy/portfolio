`timescale 1ns / 1ps
//////////////////////////////////////////////////////////////////////////////////
// Company: 
// Engineer: 
// 
// Create Date: 06/02/2024 09:29:05 PM
// Design Name: 
// Module Name: MUX
// Project Name: 
// Target Devices: 
// Tool Versions: 
// Description: 
// 
// Dependencies: 
// 
// Revision:
// Revision 0.01 - File Created
// Additional Comments:
// 
//////////////////////////////////////////////////////////////////////////////////


module MUX #(
    parameter SIZE = 32
    )
    (
        input wire [SIZE-1:0] A_in,
        input wire [SIZE-1:0] B_in,
        input wire Sel,
        output wire [SIZE-1:0] out
    );
    assign out = (Sel)? A_in : B_in;
endmodule
